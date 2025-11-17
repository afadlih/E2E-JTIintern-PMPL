<?php

namespace App\Services;

use App\Models\KapasitasLowongan;
use App\Models\Lowongan;
use App\Models\Magang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KapasitasLowonganService
{
    /**
     * Decrement available capacity when a student is accepted
     */
    public function decrementKapasitas($id_lowongan)
    {
        try {
            DB::beginTransaction();
            
            $kapasitas = KapasitasLowongan::where('id_lowongan', $id_lowongan)->lockForUpdate()->first();
            
            if (!$kapasitas) {
                // Create if doesn't exist (fallback)
                $lowongan = Lowongan::find($id_lowongan);
                if (!$lowongan) {
                    DB::rollBack();
                    return false;
                }
                
                // Count already accepted applications to determine current capacity
                $acceptedCount = Magang::where('id_lowongan', $id_lowongan)
                    ->where('status', 'aktif')
                    ->count();
                
                $kapasitas = KapasitasLowongan::create([
                    'id_lowongan' => $id_lowongan,
                    'kapasitas_tersedia' => max(0, $lowongan->kapasitas - $acceptedCount - 1), // -1 for current acceptance
                    'kapasitas_total' => $lowongan->kapasitas
                ]);
                
                DB::commit();
                return true;
            }
            
            if ($kapasitas->kapasitas_tersedia <= 0) {
                DB::rollBack();
                return false; // No capacity left
            }
            
            $kapasitas->kapasitas_tersedia -= 1;
            $kapasitas->save();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error decrementing kapasitas: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Increment available capacity when a student is rejected/canceled
     */
    public function incrementKapasitas($id_lowongan)
    {
        try {
            DB::beginTransaction();
            
            $kapasitas = KapasitasLowongan::where('id_lowongan', $id_lowongan)->lockForUpdate()->first();
            
            if (!$kapasitas) {
                // Create if doesn't exist (fallback)
                $lowongan = Lowongan::find($id_lowongan);
                if (!$lowongan) {
                    DB::rollBack();
                    return false;
                }
                
                // Count active applications to determine current usage
                $activeCount = Magang::where('id_lowongan', $id_lowongan)
                    ->where('status', 'aktif')
                    ->count();
                
                $kapasitas = KapasitasLowongan::create([
                    'id_lowongan' => $id_lowongan,
                    'kapasitas_tersedia' => $lowongan->kapasitas - $activeCount, 
                    'kapasitas_total' => $lowongan->kapasitas
                ]);
                
                DB::commit();
                return true;
            }
            
            if ($kapasitas->kapasitas_tersedia >= $kapasitas->kapasitas_total) {
                DB::rollBack();
                return true; // Already at max capacity
            }
            
            $kapasitas->kapasitas_tersedia += 1;
            $kapasitas->save();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error incrementing kapasitas: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a lowongan has available capacity
     */
    public function hasAvailableCapacity($id_lowongan)
    {
        try {
            $kapasitas = KapasitasLowongan::where('id_lowongan', $id_lowongan)->first();
            
            if (!$kapasitas) {
                $lowongan = Lowongan::find($id_lowongan);
                if (!$lowongan) {
                    return false;
                }
                
                // Count active applications to determine if capacity is available
                $activeCount = Magang::where('id_lowongan', $id_lowongan)
                    ->where('status', 'aktif')
                    ->count();
                
                return $activeCount < $lowongan->kapasitas;
            }
            
            return $kapasitas->kapasitas_tersedia > 0;
        } catch (\Exception $e) {
            Log::error('Error checking capacity: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Initialize capacity for a new lowongan
     */
    public function initializeKapasitas($id_lowongan, $total_kapasitas)
    {
        try {
            $kapasitas = KapasitasLowongan::updateOrCreate(
                ['id_lowongan' => $id_lowongan],
                [
                    'kapasitas_total' => $total_kapasitas,
                    'kapasitas_tersedia' => $total_kapasitas
                ]
            );
            
            return $kapasitas;
        } catch (\Exception $e) {
            Log::error('Error initializing kapasitas: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update capacity when lowongan is updated
     */
    public function updateKapasitasTotal($id_lowongan, $new_total)
    {
        try {
            DB::beginTransaction();
            
            $kapasitas = KapasitasLowongan::where('id_lowongan', $id_lowongan)->lockForUpdate()->first();
            
            if (!$kapasitas) {
                // If record doesn't exist, create it
                $this->initializeKapasitas($id_lowongan, $new_total);
                DB::commit();
                return true;
            }
            
            // Calculate how many positions are already filled
            $used = $kapasitas->kapasitas_total - $kapasitas->kapasitas_tersedia;
            
            // Update total capacity
            $kapasitas->kapasitas_total = $new_total;
            
            // Update available capacity (cannot be negative)
            $kapasitas->kapasitas_tersedia = max(0, $new_total - $used);
            
            $kapasitas->save();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating kapasitas total: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Recalculate and sync kapasitas based on active magang entries
     */
    public function syncKapasitas($id_lowongan)
    {
        try {
            DB::beginTransaction();
            
            $lowongan = Lowongan::find($id_lowongan);
            if (!$lowongan) {
                DB::rollBack();
                return false;
            }
            
            // Count active applications
            $activeCount = Magang::where('id_lowongan', $id_lowongan)
                ->where('status', 'aktif')
                ->count();
            
            $kapasitas = KapasitasLowongan::updateOrCreate(
                ['id_lowongan' => $id_lowongan],
                [
                    'kapasitas_total' => $lowongan->kapasitas,
                    'kapasitas_tersedia' => max(0, $lowongan->kapasitas - $activeCount)
                ]
            );
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncing kapasitas: ' . $e->getMessage());
            return false;
        }
    }
}