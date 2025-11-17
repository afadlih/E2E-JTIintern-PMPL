<?php
/* filepath: app/Http/Controllers/API/NotificationController.php */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * ✅ Get unread notification count
     */
    public function getUnreadCount()
    {
        try {
            $count = Notifikasi::forUser(Auth::id())
                ->unread()
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jumlah notifikasi',
                'count' => 0
            ], 500);
        }
    }

    /**
     * ✅ Get all notifications for current user
     */
    public function index()
    {
        try {
            $notifications = Notifikasi::forUser(Auth::id())
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id_notifikasi' => $notification->id_notifikasi,
                        'judul' => $notification->judul,
                        'pesan' => $notification->pesan,
                        'jenis' => $notification->jenis,
                        'kategori' => $notification->kategori,
                        'data_terkait' => $notification->data_terkait,
                        'is_read' => $notification->is_read,
                        'is_important' => $notification->is_important,
                        'is_expired' => $notification->is_expired,
                        'time_ago' => $notification->time_ago,
                        'formatted_date' => $notification->formatted_date,
                        'icon' => $notification->icon,
                        'badge_class' => $notification->badge_class,
                        'created_at' => $notification->created_at->toISOString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi',
                'data' => []
            ], 500);
        }
    }

    /**
     * ✅ Get specific notification detail
     */
    public function show($id)
    {
        try {
            $notification = Notifikasi::forUser(Auth::id())
                ->where('id_notifikasi', $id)
                ->with('user')
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            $data = [
                'id_notifikasi' => $notification->id_notifikasi,
                'judul' => $notification->judul,
                'pesan' => $notification->pesan,
                'jenis' => $notification->jenis,
                'kategori' => $notification->kategori,
                'data_terkait' => $notification->data_terkait,
                'is_read' => $notification->is_read,
                'is_important' => $notification->is_important,
                'is_expired' => $notification->is_expired,
                'time_ago' => $notification->time_ago,
                'formatted_date' => $notification->formatted_date,
                'icon' => $notification->icon,
                'badge_class' => $notification->badge_class,
                'expired_at' => $notification->expired_at,
                'created_at' => $notification->created_at->toISOString(),
                'user' => [
                    'id' => $notification->user->id_user,
                    'name' => $notification->user->name,
                    'email' => $notification->user->email
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting notification detail: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail notifikasi'
            ], 500);
        }
    }

    /**
     * ✅ Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notifikasi::forUser(Auth::id())
                ->where('id_notifikasi', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            $notification->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi sebagai dibaca'
            ], 500);
        }
    }

    /**
     * ✅ Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $updated = Notifikasi::forUser(Auth::id())
                ->unread()
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sebagai dibaca',
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi sebagai dibaca'
            ], 500);
        }
    }

    /**
     * ✅ Delete specific notification
     */
    public function destroy($id)
    {
        try {
            $notification = Notifikasi::forUser(Auth::id())
                ->where('id_notifikasi', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi'
            ], 500);
        }
    }

    /**
     * ✅ Clear all notifications
     */
    public function clearAll()
    {
        try {
            $deleted = Notifikasi::forUser(Auth::id())->count();
            Notifikasi::forUser(Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil dihapus',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing all notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua notifikasi'
            ], 500);
        }
    }

    /**
     * ✅ Clear read notifications
     */
    public function clearRead()
    {
        try {
            $deleted = Notifikasi::forUser(Auth::id())
                ->where('is_read', true)
                ->count();
            
            Notifikasi::forUser(Auth::id())
                ->where('is_read', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi yang dibaca berhasil dihapus',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing read notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi yang dibaca'
            ], 500);
        }
    }

    /**
     * ✅ Clear expired notifications
     */
    public function clearExpired()
    {
        try {
            $deleted = Notifikasi::forUser(Auth::id())
                ->where('expired_at', '<', now())
                ->count();
            
            Notifikasi::forUser(Auth::id())
                ->where('expired_at', '<', now())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi kedaluwarsa berhasil dihapus',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing expired notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi kedaluwarsa'
            ], 500);
        }
    }
}
