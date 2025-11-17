<?php
/* filepath: app/Services/NotificationService.php */

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;

class NotificationService
{
    /**
     * ✅ Create notification untuk user tertentu
     */
    public function createNotification(
        $userId, 
        $judul, 
        $pesan, 
        $kategori = 'sistem', 
        $jenis = 'info',
        $isImportant = false,
        $dataTerkait = null,
        $expiredDays = 7
    ) {
        return Notifikasi::create([
            'id_user' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'kategori' => $kategori,
            'jenis' => $jenis,
            'is_important' => $isImportant,
            'data_terkait' => $dataTerkait,
            'expired_at' => $expiredDays ? now()->addDays($expiredDays) : null
        ]);
    }

    /**
     * ✅ Notification untuk lamaran diterima
     */
    public function lamaranDiterima($userId, $namaPerusahaan, $posisi, $lamaranId)
    {
        return $this->createNotification(
            $userId,
            'Lamaran Diterima! 🎉',
            "Selamat! Lamaran Anda untuk posisi {$posisi} di {$namaPerusahaan} telah diterima. Silakan cek detail selanjutnya.",
            'lamaran',
            'success',
            true,
            ['lamaran_id' => $lamaranId, 'action' => 'accepted'],
            14 // 2 minggu
        );
    }

    /**
     * ✅ Notification untuk lamaran ditolak
     */
    public function lamaranDitolak($userId, $namaPerusahaan, $posisi, $alasan = null)
    {
        $pesan = "Lamaran Anda untuk posisi {$posisi} di {$namaPerusahaan} tidak dapat dilanjutkan.";
        if ($alasan) {
            $pesan .= " Alasan: {$alasan}";
        }
        $pesan .= " Jangan menyerah, coba lowongan lainnya!";

        return $this->createNotification(
            $userId,
            'Update Lamaran',
            $pesan,
            'lamaran',
            'warning',
            false,
            ['action' => 'rejected'],
            7
        );
    }

    /**
     * ✅ Notification untuk deadline evaluasi
     */
    public function deadlineEvaluasi($userId, $deadline)
    {
        return $this->createNotification(
            $userId,
            'Reminder: Deadline Evaluasi',
            "Jangan lupa untuk mengisi evaluasi magang Anda. Deadline: {$deadline->format('d M Y')}",
            'deadline',
            'warning',
            true,
            ['deadline' => $deadline->toDateString()],
            3
        );
    }

    /**
     * ✅ Notification untuk pengumuman umum
     */
    public function pengumumanUmum($judul, $pesan, $userIds = [])
    {
        $notifications = [];
        
        // Jika tidak ada user spesifik, kirim ke semua mahasiswa
        if (empty($userIds)) {
            $userIds = User::where('role', 'mahasiswa')->pluck('id_user')->toArray();
        }

        foreach ($userIds as $userId) {
            $notifications[] = $this->createNotification(
                $userId,
                $judul,
                $pesan,
                'pengumuman',
                'info',
                true,
                null,
                30 // 1 bulan
            );
        }

        return $notifications;
    }

    /**
     * ✅ Get unread count untuk user
     */
    public function getUnreadCount($userId)
    {
        return Notifikasi::where('id_user', $userId)
            ->unread()
            ->active()
            ->count();
    }

    /**
     * ✅ Get notifications untuk user
     */
    public function getUserNotifications($userId, $limit = 10, $onlyUnread = false)
    {
        $query = Notifikasi::where('id_user', $userId)
            ->active()
            ->orderByDesc('is_important')
            ->orderByDesc('created_at');

        if ($onlyUnread) {
            $query->unread();
        }

        return $query->limit($limit)->get();
    }

    /**
     * ✅ Mark all as read
     */
    public function markAllAsRead($userId)
    {
        return Notifikasi::where('id_user', $userId)
            ->unread()
            ->update(['is_read' => true]);
    }

    /**
     * ✅ Clean up expired notifications
     */
    public function cleanupExpired()
    {
        return Notifikasi::where('expired_at', '<', now())->delete();
    }
}