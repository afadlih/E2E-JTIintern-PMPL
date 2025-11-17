<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LogbookPhotoService
{
    protected $disk = 'public';
    protected $baseFolder = 'logbook';
    
    /**
     * Store logbook photo sederhana tanpa optimization
     */
    public function storePhoto(UploadedFile $file, $magangId, $tanggal = null): array
    {
        try {
            $tanggal = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
            
            // ✅ STRUKTUR FOLDER: logbook/YYYY/MM/
            $folderPath = $this->baseFolder . '/' . $tanggal->format('Y') . '/' . $tanggal->format('m');
            
            // ✅ GENERATE FILENAME yang unik
            $fileName = $this->generateFileName($file, $magangId, $tanggal);
            
            // ✅ VALIDASI file
            $this->validateFile($file);
            
            // ✅ PERBAIKAN: Gunakan storeAs yang benar
            $filePath = $file->storeAs($folderPath, $fileName, $this->disk);
            
            if (!$filePath) {
                throw new \Exception('Gagal menyimpan foto ke storage');
            }
            
            Log::info('Logbook photo stored successfully:', [
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $filePath,
                'file_size' => $file->getSize(),
                'magang_id' => $magangId
            ]);
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
                'url' => $this->getPhotoUrl($filePath)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error storing logbook photo: ' . $e->getMessage(), [
                'file_name' => $file->getClientOriginalName(),
                'magang_id' => $magangId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate nama file yang unik dan SEO friendly
     */
    private function generateFileName(UploadedFile $file, $magangId, Carbon $tanggal): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (empty($extension)) {
            $extension = 'jpg';
        }
        
        $timestamp = $tanggal->format('Ymd_His');
        $random = substr(md5(uniqid()), 0, 8);
        
        return "logbook_magang{$magangId}_{$timestamp}_{$random}.{$extension}";
    }
    
    /**
     * Validasi file upload yang diperlukan
     */
    private function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \Exception('File upload tidak valid');
        }
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Tipe file tidak didukung. Gunakan: JPG, PNG, GIF, atau WebP');
        }
        
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('MIME type file tidak valid');
        }
        
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $maxSize) {
            $fileSizeMB = round($file->getSize() / (1024 * 1024), 2);
            throw new \Exception("Ukuran file terlalu besar ({$fileSizeMB}MB). Maksimal 5MB");
        }
        
        if ($file->getSize() < 1024) {
            throw new \Exception('File terlalu kecil atau corrupt');
        }
    }
    
    /**
     * Get public URL untuk foto
     */
    public function getPhotoUrl(string $filePath): string
    {
        $cleanPath = ltrim($filePath, '/');
        return asset('storage/' . $cleanPath);
    }
    
    /**
     * Delete foto
     */
    public function deletePhoto(string $filePath): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($filePath)) {
                $deleted = Storage::disk($this->disk)->delete($filePath);
                
                Log::info('Logbook photo deleted successfully:', [
                    'file_path' => $filePath,
                    'deleted' => $deleted
                ]);
                
                return $deleted;
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error deleting logbook photo: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Cek apakah file ada dan get info
     */
    public function getPhotoInfo(string $filePath): array
    {
        try {
            if (!Storage::disk($this->disk)->exists($filePath)) {
                return [
                    'exists' => false,
                    'url' => null
                ];
            }
            
            $size = Storage::disk($this->disk)->size($filePath);
            $lastModified = Storage::disk($this->disk)->lastModified($filePath);
            $mimeType = $this->getMimeTypeFromExtension($filePath);
            
            return [
                'exists' => true,
                'size' => $size,
                'last_modified' => $lastModified,
                'url' => $this->getPhotoUrl($filePath),
                'mime_type' => $mimeType
            ];
            
        } catch (\Exception $e) {
            Log::warning('Error getting photo info: ' . $e->getMessage());
            return [
                'exists' => false,
                'url' => null,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Helper untuk get mime type dari extension
     */
    private function getMimeTypeFromExtension(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        return $mimeTypes[$extension] ?? 'image/jpeg';
    }
    
    /**
     * ✅ PERBAIKAN: Helper untuk validasi apakah file adalah gambar
     */
    public function isValidImage(string $filePath): bool
    {
        try {
            if (!Storage::disk($this->disk)->exists($filePath)) {
                return false;
            }
            
            // ✅ FIX: Bangun path file secara manual untuk disk 'public'
            $fullPath = storage_path('app/public/' . ltrim($filePath, '/'));
            
            if (!file_exists($fullPath)) {
                return false;
            }
            
            $imageInfo = getimagesize($fullPath);
            
            return $imageInfo !== false;
            
        } catch (\Exception $e) {
            Log::warning('Error validating image: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ✅ PERBAIKAN: Group log entries dengan debug path yang lebih detail
     */
    private function groupLogEntriesByMonth($logEntries)
    {
        $grouped = [];

        foreach ($logEntries as $entry) {
            $date = Carbon::parse($entry->tanggal);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('F Y');

            if (!isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [
                    'month' => $monthLabel,
                    'entries' => []
                ];
            }

            // ✅ ENHANCED: Generate URL foto dengan debug
            $fotoUrl = null;
            $hasFoto = false;
            $debugInfo = null;

            if ($entry->foto) {
                // ✅ GUNAKAN: PhotoService untuk get info
                $photoInfo = $this->getPhotoInfo($entry->foto);
                
                if ($photoInfo['exists']) {
                    $fotoUrl = $photoInfo['url'];
                    $hasFoto = true;
                    
                    Log::info('Photo found for timeline:', [
                        'entry_id' => $entry->id_log,
                        'foto_path_db' => $entry->foto,
                        'foto_url' => $fotoUrl,
                        'photo_info' => $photoInfo
                    ]);
                } else {
                    // ✅ ENHANCED DEBUG: Cari file di semua kemungkinan lokasi
                    $alternativePaths = [
                        $entry->foto,
                        ltrim($entry->foto, '/'),
                        'logbook/' . basename($entry->foto),
                        'logbook/2025/06/' . basename($entry->foto)
                    ];
                    
                    $foundPath = null;
                    foreach ($alternativePaths as $altPath) {
                        if (Storage::disk('public')->exists($altPath)) {
                            $foundPath = $altPath;
                            $fotoUrl = asset('storage/' . $altPath);
                            $hasFoto = true;
                            break;
                        }
                    }
                    
                    $debugInfo = [
                        'original_path' => $entry->foto,
                        'alternatives_checked' => $alternativePaths,
                        'found_path' => $foundPath,
                        'photo_service_info' => $photoInfo
                    ];
                    
                    Log::warning('Photo file not found, searched alternatives:', $debugInfo);
                }
            }

            $grouped[$monthKey]['entries'][] = [
                'id' => $entry->id_log,
                'tanggal' => $entry->tanggal,
                'tanggal_formatted' => $date->format('d M Y'),
                'tanggal_hari' => $date->format('l'),
                'deskripsi' => $entry->log_aktivitas,
                'foto' => $fotoUrl,
                'has_foto' => $hasFoto,
                'debug_info' => $debugInfo, // ✅ INFO debug untuk frontend
                'created_at' => $entry->created_at,
                'time_ago' => Carbon::parse($entry->created_at)->diffForHumans()
            ];
        }

        krsort($grouped);
        return array_values($grouped);
    }
}