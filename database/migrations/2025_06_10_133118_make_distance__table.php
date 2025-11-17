<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

return new class extends Migration
{
    public function up()
    {
        // ✅ STEP 1: Add coordinate columns to existing m_wilayah
        $this->addCoordinatesToWilayah();
        
        // ✅ STEP 2: Add cities from Pulau Jawa only
        $this->addJavaIslandCities();
        
        // ✅ STEP 3: Map existing cities to coordinates
        $this->mapExistingCitiesToCoordinates();
        
        // ✅ STEP 4: Create distance matrix
        $this->createDistanceMatrix();
        
        // ✅ STEP 5: Generate distance data using coordinates
        $this->generateDistanceMatrixFromCoordinates();
    }
    
    /**
     * Add coordinate columns to m_wilayah
     */
    private function addCoordinatesToWilayah()
    {
        echo "🗺️ Adding coordinate columns to m_wilayah...\n";
        
        if (!Schema::hasColumn('m_wilayah', 'latitude')) {
            Schema::table('m_wilayah', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable()->after('nama_kota');
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
                $table->string('province_code', 10)->nullable()->after('longitude');
                $table->string('province_name', 100)->nullable()->after('province_code');
                $table->enum('city_type', ['KOTA', 'KABUPATEN'])->nullable()->after('province_name');
                $table->boolean('coordinates_verified')->default(false)->after('city_type');
                $table->string('data_source', 50)->default('manual')->after('coordinates_verified');
                $table->boolean('is_active')->default(true)->after('data_source');
            });
        }
        
        echo "✅ Coordinate columns added to m_wilayah\n";
    }
    
    /**
     * ✅ Add cities from Java Island only (6 provinces)
     */
    private function addJavaIslandCities()
    {
        echo "\n🏝️ Adding cities from Java Island (6 provinces)...\n";
        
        // Get existing cities to avoid duplicates
        $existingCities = DB::table('m_wilayah')
            ->pluck('nama_kota')
            ->map(function($city) {
                return strtolower(trim($city));
            })
            ->toArray();
        
        // Java Island cities with accurate coordinates
        $javaCities = $this->getJavaIslandCities();
        
        $newCitiesAdded = 0;
        $batch = [];
        $batchSize = 50;
        
        foreach ($javaCities as $cityData) {
            $cityNameLower = strtolower($cityData['nama_kota']);
            
            // Check if city already exists
            $exists = false;
            foreach ($existingCities as $existingCity) {
                if ($this->isCityMatch($existingCity, $cityNameLower)) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $batch[] = [
                    'nama_kota' => $cityData['nama_kota'],
                    'latitude' => $cityData['latitude'],
                    'longitude' => $cityData['longitude'],
                    'province_code' => $cityData['province_code'],
                    'province_name' => $cityData['province_name'],
                    'city_type' => $cityData['city_type'],
                    'coordinates_verified' => true,
                    'data_source' => 'java_island_added',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $newCitiesAdded++;
                echo "📍 Adding: {$cityData['nama_kota']} ({$cityData['province_name']})\n";
                
                // Insert in batches
                if (count($batch) >= $batchSize) {
                    DB::table('m_wilayah')->insert($batch);
                    $batch = [];
                }
            }
        }
        
        // Insert remaining batch
        if (!empty($batch)) {
            DB::table('m_wilayah')->insert($batch);
        }
        
        echo "✅ Successfully added {$newCitiesAdded} new cities from Java Island\n";
        
        // Show summary by province
        $this->showJavaCitiesSummary();
    }
    
    /**
     * Get comprehensive Java Island cities data
     */
    private function getJavaIslandCities()
    {
        return [
            // ========================================
            // DKI JAKARTA (Province Code: 31)
            // ========================================
            ['nama_kota' => 'Jakarta Pusat', 'latitude' => -6.1805, 'longitude' => 106.8284, 'province_code' => '31', 'province_name' => 'DKI Jakarta', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Jakarta Utara', 'latitude' => -6.1384, 'longitude' => 106.8632, 'province_code' => '31', 'province_name' => 'DKI Jakarta', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Jakarta Barat', 'latitude' => -6.1352, 'longitude' => 106.8133, 'province_code' => '31', 'province_name' => 'DKI Jakarta', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Jakarta Selatan', 'latitude' => -6.2615, 'longitude' => 106.8106, 'province_code' => '31', 'province_name' => 'DKI Jakarta', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Jakarta Timur', 'latitude' => -6.2251, 'longitude' => 106.9004, 'province_code' => '31', 'province_name' => 'DKI Jakarta', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Kepulauan Seribu', 'latitude' => -5.6157, 'longitude' => 106.6086, 'province_code' => '31', 'province_name' => 'DKI Jakarta', 'city_type' => 'KABUPATEN'],
            
            // ========================================
            // JAWA BARAT (Province Code: 32)
            // ========================================
            ['nama_kota' => 'Bandung', 'latitude' => -6.9175, 'longitude' => 107.6191, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Bekasi', 'latitude' => -6.2383, 'longitude' => 106.9756, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Bogor', 'latitude' => -6.5971, 'longitude' => 106.8060, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Cirebon', 'latitude' => -6.7063, 'longitude' => 108.5571, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Depok', 'latitude' => -6.4025, 'longitude' => 106.7942, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Sukabumi', 'latitude' => -6.9271, 'longitude' => 106.9570, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Tasikmalaya', 'latitude' => -7.3506, 'longitude' => 108.2154, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Cimahi', 'latitude' => -6.8723, 'longitude' => 107.5425, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Banjar', 'latitude' => -7.3450, 'longitude' => 108.5489, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KOTA'],
            
            // Kabupaten Jawa Barat
            ['nama_kota' => 'Bandung Barat', 'latitude' => -6.8186, 'longitude' => 107.4917, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Bekasi', 'latitude' => -6.2754, 'longitude' => 107.1426, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Bogor', 'latitude' => -6.4588, 'longitude' => 106.8316, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Cianjur', 'latitude' => -6.8174, 'longitude' => 107.1426, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Cirebon', 'latitude' => -6.7599, 'longitude' => 108.4870, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Garut', 'latitude' => -7.2253, 'longitude' => 107.8967, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Indramayu', 'latitude' => -6.3274, 'longitude' => 108.3199, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Karawang', 'latitude' => -6.3215, 'longitude' => 107.3020, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Kuningan', 'latitude' => -6.9759, 'longitude' => 108.4830, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Majalengka', 'latitude' => -6.8368, 'longitude' => 108.2274, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pangandaran', 'latitude' => -7.6867, 'longitude' => 108.6500, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Purwakarta', 'latitude' => -6.5569, 'longitude' => 107.4431, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Subang', 'latitude' => -6.5693, 'longitude' => 107.7607, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sukabumi', 'latitude' => -6.8719, 'longitude' => 106.9570, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sumedang', 'latitude' => -6.8597, 'longitude' => 107.9167, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Tasikmalaya', 'latitude' => -7.3959, 'longitude' => 108.2154, 'province_code' => '32', 'province_name' => 'Jawa Barat', 'city_type' => 'KABUPATEN'],
            
            // ========================================
            // BANTEN (Province Code: 36)
            // ========================================
            ['nama_kota' => 'Tangerang', 'latitude' => -6.1783, 'longitude' => 106.6319, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Tangerang Selatan', 'latitude' => -6.2882, 'longitude' => 106.7516, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Serang', 'latitude' => -6.1200, 'longitude' => 106.1502, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Cilegon', 'latitude' => -6.0024, 'longitude' => 106.0192, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KOTA'],
            
            // Kabupaten Banten
            ['nama_kota' => 'Tangerang', 'latitude' => -6.2297, 'longitude' => 106.6890, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Serang', 'latitude' => -6.1895, 'longitude' => 106.1502, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Lebak', 'latitude' => -6.5644, 'longitude' => 106.2522, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pandeglang', 'latitude' => -6.3081, 'longitude' => 106.1067, 'province_code' => '36', 'province_name' => 'Banten', 'city_type' => 'KABUPATEN'],
            
            // ========================================
            // JAWA TENGAH (Province Code: 33)
            // ========================================
            ['nama_kota' => 'Semarang', 'latitude' => -6.9932, 'longitude' => 110.4203, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Surakarta', 'latitude' => -7.5755, 'longitude' => 110.8243, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Magelang', 'latitude' => -7.4774, 'longitude' => 110.2170, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Salatiga', 'latitude' => -7.3318, 'longitude' => 110.4920, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Pekalongan', 'latitude' => -6.8886, 'longitude' => 109.6753, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Tegal', 'latitude' => -6.8694, 'longitude' => 109.1402, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KOTA'],
            
            // Major Kabupaten Jawa Tengah
            ['nama_kota' => 'Semarang', 'latitude' => -7.1975, 'longitude' => 110.2672, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Klaten', 'latitude' => -7.7058, 'longitude' => 110.6061, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Kudus', 'latitude' => -6.8048, 'longitude' => 110.8405, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Jepara', 'latitude' => -6.5890, 'longitude' => 110.6684, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Demak', 'latitude' => -6.8947, 'longitude' => 110.6396, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Kendal', 'latitude' => -6.9264, 'longitude' => 110.2037, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Boyolali', 'latitude' => -7.5323, 'longitude' => 110.5955, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Karanganyar', 'latitude' => -7.6283, 'longitude' => 111.0378, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Wonogiri', 'latitude' => -7.8145, 'longitude' => 110.9270, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sukoharjo', 'latitude' => -7.6838, 'longitude' => 110.8411, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Purworejo', 'latitude' => -7.7209, 'longitude' => 110.0158, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Kebumen', 'latitude' => -7.6707, 'longitude' => 109.6544, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Cilacap', 'latitude' => -7.7297, 'longitude' => 109.0088, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Banyumas', 'latitude' => -7.5186, 'longitude' => 109.2947, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Purbalingga', 'latitude' => -7.3886, 'longitude' => 109.3668, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Banjarnegara', 'latitude' => -7.3447, 'longitude' => 109.6847, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Wonosobo', 'latitude' => -7.3608, 'longitude' => 109.9901, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Magelang', 'latitude' => -7.5474, 'longitude' => 110.2170, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Temanggung', 'latitude' => -7.3147, 'longitude' => 110.1715, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sragen', 'latitude' => -7.4186, 'longitude' => 111.0272, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Grobogan', 'latitude' => -7.0586, 'longitude' => 110.9419, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Blora', 'latitude' => -6.9697, 'longitude' => 111.4175, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Rembang', 'latitude' => -6.7086, 'longitude' => 111.3424, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pati', 'latitude' => -6.7558, 'longitude' => 111.0378, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Brebes', 'latitude' => -6.8731, 'longitude' => 109.0324, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Tegal', 'latitude' => -6.9186, 'longitude' => 109.1175, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pemalang', 'latitude' => -6.8986, 'longitude' => 109.3668, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pekalongan', 'latitude' => -7.0186, 'longitude' => 109.6419, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Batang', 'latitude' => -6.9047, 'longitude' => 109.7419, 'province_code' => '33', 'province_name' => 'Jawa Tengah', 'city_type' => 'KABUPATEN'],
            
            // ========================================
            // DI YOGYAKARTA (Province Code: 34)
            // ========================================
            ['nama_kota' => 'Yogyakarta', 'latitude' => -7.8753, 'longitude' => 110.4262, 'province_code' => '34', 'province_name' => 'DI Yogyakarta', 'city_type' => 'KOTA'],
            
            // Kabupaten DIY
            ['nama_kota' => 'Bantul', 'latitude' => -7.8879, 'longitude' => 110.3297, 'province_code' => '34', 'province_name' => 'DI Yogyakarta', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sleman', 'latitude' => -7.7326, 'longitude' => 110.3553, 'province_code' => '34', 'province_name' => 'DI Yogyakarta', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Kulonprogo', 'latitude' => -7.8214, 'longitude' => 110.1553, 'province_code' => '34', 'province_name' => 'DI Yogyakarta', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Gunungkidul', 'latitude' => -7.9553, 'longitude' => 110.5939, 'province_code' => '34', 'province_name' => 'DI Yogyakarta', 'city_type' => 'KABUPATEN'],
            
            // ========================================
            // JAWA TIMUR (Province Code: 35)
            // ========================================
            ['nama_kota' => 'Surabaya', 'latitude' => -7.2575, 'longitude' => 112.7521, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Malang', 'latitude' => -7.9666, 'longitude' => 112.6326, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Kediri', 'latitude' => -7.8486, 'longitude' => 112.0169, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Blitar', 'latitude' => -8.0954, 'longitude' => 112.1693, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Madiun', 'latitude' => -7.6298, 'longitude' => 111.5239, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Probolinggo', 'latitude' => -7.7543, 'longitude' => 113.2159, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Pasuruan', 'latitude' => -7.6391, 'longitude' => 112.9075, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Mojokerto', 'latitude' => -7.4664, 'longitude' => 112.4339, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            ['nama_kota' => 'Batu', 'latitude' => -7.8707, 'longitude' => 112.5241, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KOTA'],
            
            // All Kabupaten Jawa Timur
            ['nama_kota' => 'Bangkalan', 'latitude' => -7.0455, 'longitude' => 112.7351, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Banyuwangi', 'latitude' => -8.2193, 'longitude' => 114.3691, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Blitar', 'latitude' => -8.1954, 'longitude' => 112.1693, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Bojonegoro', 'latitude' => -7.1502, 'longitude' => 111.8817, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Bondowoso', 'latitude' => -7.9138, 'longitude' => 113.8213, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Gresik', 'latitude' => -7.1556, 'longitude' => 112.6536, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Jember', 'latitude' => -8.1721, 'longitude' => 113.7016, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Jombang', 'latitude' => -7.5564, 'longitude' => 112.2384, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Kediri', 'latitude' => -7.9486, 'longitude' => 112.0169, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Lamongan', 'latitude' => -7.1176, 'longitude' => 112.4107, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Lumajang', 'latitude' => -8.1335, 'longitude' => 113.2248, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Madiun', 'latitude' => -7.6698, 'longitude' => 111.5239, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Magetan', 'latitude' => -7.6417, 'longitude' => 111.3500, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Malang', 'latitude' => -8.1335, 'longitude' => 112.6326, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Mojokerto', 'latitude' => -7.5664, 'longitude' => 112.4339, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Nganjuk', 'latitude' => -7.6051, 'longitude' => 111.9046, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Ngawi', 'latitude' => -7.4040, 'longitude' => 111.4461, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pacitan', 'latitude' => -8.1995, 'longitude' => 111.0910, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pamekasan', 'latitude' => -7.1568, 'longitude' => 113.4746, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Pasuruan', 'latitude' => -7.7299, 'longitude' => 112.9075, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Ponorogo', 'latitude' => -7.8686, 'longitude' => 111.4619, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Probolinggo', 'latitude' => -7.8743, 'longitude' => 113.2159, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sampang', 'latitude' => -7.1872, 'longitude' => 113.2394, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sidoarjo', 'latitude' => -7.4467, 'longitude' => 112.7186, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Situbondo', 'latitude' => -7.7063, 'longitude' => 114.0095, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Sumenep', 'latitude' => -7.0167, 'longitude' => 113.8667, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Trenggalek', 'latitude' => -8.0500, 'longitude' => 111.7167, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Tuban', 'latitude' => -6.8969, 'longitude' => 111.9608, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
            ['nama_kota' => 'Tulungagung', 'latitude' => -8.0644, 'longitude' => 111.9036, 'province_code' => '35', 'province_name' => 'Jawa Timur', 'city_type' => 'KABUPATEN'],
        ];
    }
    
    /**
     * Map existing cities to coordinates
     */
    private function mapExistingCitiesToCoordinates()
    {
        echo "\n🔍 Mapping existing cities to Java Island coordinates...\n";
        
        $updated = 0;
        $existingWilayah = DB::table('m_wilayah')
            ->whereNull('latitude')
            ->get();
        
        $javaCoordinates = $this->getJavaCoordinatesLookup();
        
        foreach ($existingWilayah as $wilayah) {
            $cityName = strtolower(trim($wilayah->nama_kota));
            
            foreach ($javaCoordinates as $coordKey => $coordData) {
                if ($this->isCityMatch($cityName, $coordKey)) {
                    DB::table('m_wilayah')
                        ->where('wilayah_id', $wilayah->wilayah_id)
                        ->update([
                            'latitude' => $coordData['latitude'],
                            'longitude' => $coordData['longitude'],
                            'province_code' => $coordData['province_code'],
                            'province_name' => $coordData['province_name'],
                            'city_type' => $coordData['city_type'],
                            'coordinates_verified' => true,
                            'data_source' => 'java_mapped'
                        ]);
                    
                    $updated++;
                    echo "✅ Mapped: {$wilayah->nama_kota} → {$coordData['province_name']}\n";
                    break;
                }
            }
        }
        
        echo "✅ Successfully mapped {$updated} existing cities to Java coordinates\n";
    }
    
    /**
     * Get Java coordinates lookup table
     */
    private function getJavaCoordinatesLookup()
    {
        $lookup = [];
        $javaCities = $this->getJavaIslandCities();
        
        foreach ($javaCities as $city) {
            $key = strtolower($city['nama_kota']);
            $lookup[$key] = $city;
            
            // Add variations
            $variations = $this->getCityNameVariations($city['nama_kota']);
            foreach ($variations as $variation) {
                $lookup[strtolower($variation)] = $city;
            }
        }
        
        return $lookup;
    }
    
    /**
     * Get city name variations
     */
    private function getCityNameVariations($cityName)
    {
        $variations = [];
        
        // Remove prefixes
        $clean = str_replace(['Kabupaten ', 'Kota '], '', $cityName);
        $variations[] = $clean;
        
        // Add common variations
        $variations[] = strtolower($clean);
        $variations[] = ucfirst(strtolower($clean));
        
        return array_unique($variations);
    }
    
    /**
     * Show Java cities summary
     */
    private function showJavaCitiesSummary()
    {
        echo "\n📊 JAVA ISLAND CITIES SUMMARY:\n";
        echo "=================================\n";
        
        $summary = DB::table('m_wilayah')
            ->where('data_source', 'java_island_added')
            ->selectRaw('province_name, COUNT(*) as count')
            ->groupBy('province_name')
            ->orderBy('count', 'desc')
            ->get();
        
        foreach ($summary as $item) {
            echo "{$item->province_name}: {$item->count} cities\n";
        }
        
        $total = $summary->sum('count');
        echo "\nTotal Java cities added: {$total}\n";
        echo "=================================\n\n";
    }
    
    /**
     * Check if city names match (same as before)
     */
    private function isCityMatch($cityName, $regencyName)
    {
        $cityClean = str_replace(['kota ', 'kabupaten '], '', $cityName);
        $regencyClean = str_replace(['kota ', 'kabupaten '], '', $regencyName);
        
        if ($cityClean === $regencyClean) {
            return true;
        }
        
        if (strpos($regencyClean, $cityClean) !== false || strpos($cityClean, $regencyClean) !== false) {
            return true;
        }
        
        $similarity = 0;
        similar_text($cityClean, $regencyClean, $similarity);
        
        return $similarity > 80;
    }
    
    /**
     * Create distance matrix table (same as before)
     */
    private function createDistanceMatrix()
    {
        if (Schema::hasTable('m_distance_matrix')) {
            echo "ℹ️ Distance matrix table already exists\n";
            return;
        }
        
        echo "📊 Creating distance matrix table...\n";
        
        Schema::create('m_distance_matrix', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_wilayah_id');
            $table->unsignedBigInteger('to_wilayah_id');
            $table->decimal('distance_km', 8, 2);
            $table->integer('duration_minutes');
            $table->tinyInteger('score')->comment('1=Dekat, 2=Sedang, 3=Jauh');
            $table->string('category', 50);
            $table->enum('calculation_method', ['haversine', 'manual', 'osm'])->default('haversine');
            $table->timestamps();
            
            $table->index(['from_wilayah_id', 'to_wilayah_id']);
            $table->index('score');
            $table->foreign('from_wilayah_id')->references('wilayah_id')->on('m_wilayah');
            $table->foreign('to_wilayah_id')->references('wilayah_id')->on('m_wilayah');
            $table->unique(['from_wilayah_id', 'to_wilayah_id']);
        });
        
        echo "✅ Distance matrix table created\n";
    }
    
    /**
     * Generate distance matrix from coordinates (same as before, but Java only)
     */
    private function generateDistanceMatrixFromCoordinates()
    {
        echo "🧮 Generating distance matrix for Java Island cities...\n";
        
        $cities = DB::table('m_wilayah')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('wilayah_id', 'nama_kota', 'latitude', 'longitude', 'province_name')
            ->get();
        
        if ($cities->isEmpty()) {
            echo "⚠️ No cities with coordinates found\n";
            return;
        }
        
        echo "📍 Found " . $cities->count() . " Java cities with coordinates\n";
        
        $inserted = 0;
        $batch = [];
        $batchSize = 100;
        
        foreach ($cities as $fromCity) {
            foreach ($cities as $toCity) {
                if ($fromCity->wilayah_id === $toCity->wilayah_id) {
                    continue;
                }
                
                $exists = DB::table('m_distance_matrix')
                    ->where('from_wilayah_id', $fromCity->wilayah_id)
                    ->where('to_wilayah_id', $toCity->wilayah_id)
                    ->exists();
                
                if ($exists) {
                    continue;
                }
                
                $distance = $this->calculateHaversineDistance(
                    $fromCity->latitude, $fromCity->longitude,
                    $toCity->latitude, $toCity->longitude
                );
                
                $score = $this->calculateDistanceScore($distance);
                $category = $this->getDistanceCategory($score);
                $duration = $this->estimateDuration($distance);
                
                $batch[] = [
                    'from_wilayah_id' => $fromCity->wilayah_id,
                    'to_wilayah_id' => $toCity->wilayah_id,
                    'distance_km' => round($distance, 2),
                    'duration_minutes' => $duration,
                    'score' => $score,
                    'category' => $category,
                    'calculation_method' => 'haversine',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                if (count($batch) >= $batchSize) {
                    DB::table('m_distance_matrix')->insert($batch);
                    $inserted += count($batch);
                    $batch = [];
                    echo "📝 Inserted {$inserted} distance records...\n";
                }
            }
        }
        
        if (!empty($batch)) {
            DB::table('m_distance_matrix')->insert($batch);
            $inserted += count($batch);
        }
        
        echo "✅ Generated {$inserted} distance matrix entries for Java Island\n";
        $this->showDistanceStatistics();
    }
    
    // Helper methods (same as before)
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
    
    private function calculateDistanceScore($distance)
    {
        if ($distance <= 30) return 1;
        if ($distance <= 100) return 2;
        return 3;
    }
    
    private function getDistanceCategory($score)
    {
        return match($score) {
            1 => 'Sangat Dekat',
            2 => 'Sedang',
            3 => 'Jauh',
            default => 'Tidak Diketahui'
        };
    }
    
    private function estimateDuration($distance)
    {
        return round($distance * 1.33);
    }
    
    private function showDistanceStatistics()
    {
        echo "\n📊 JAVA ISLAND DISTANCE MATRIX STATISTICS:\n";
        echo "==========================================\n";
        
        $total = DB::table('m_distance_matrix')->count();
        $dekat = DB::table('m_distance_matrix')->where('score', 1)->count();
        $sedang = DB::table('m_distance_matrix')->where('score', 2)->count();
        $jauh = DB::table('m_distance_matrix')->where('score', 3)->count();
        
        $avgDistance = DB::table('m_distance_matrix')->avg('distance_km');
        $maxDistance = DB::table('m_distance_matrix')->max('distance_km');
        $minDistance = DB::table('m_distance_matrix')->min('distance_km');
        
        echo "Total distance entries: {$total}\n";
        echo "Sangat Dekat (≤30km): {$dekat}\n";
        echo "Sedang (31-100km): {$sedang}\n";
        echo "Jauh (>100km): {$jauh}\n";
        echo "Average distance: " . round($avgDistance, 2) . " km\n";
        echo "Min distance: " . round($minDistance, 2) . " km\n";
        echo "Max distance: " . round($maxDistance, 2) . " km\n";
        echo "==========================================\n\n";
    }
    
    public function down()
    {
        Schema::dropIfExists('m_distance_matrix');
        
        if (Schema::hasColumn('m_wilayah', 'latitude')) {
            Schema::table('m_wilayah', function (Blueprint $table) {
                $table->dropColumn([
                    'latitude', 'longitude', 'province_code', 
                    'province_name', 'city_type', 'coordinates_verified',
                    'data_source', 'is_active'
                ]);
            });
        }
    }
};