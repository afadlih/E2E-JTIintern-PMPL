<?php
/* filepath: app/Models/Notifikasi.php */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notifikasi extends Model
{
    protected $table = 'm_notifikasi';
    protected $primaryKey = 'id_notifikasi';
    
    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'jenis',
        'kategori',
        'data_terkait',
        'is_read',
        'is_important',
        'expired_at'
    ];

    protected $casts = [
        'data_terkait' => 'array',
        'is_read' => 'boolean',
        'is_important' => 'boolean',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ✅ Relationship dengan User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // ✅ Accessor untuk time_ago
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // ✅ Accessor untuk formatted date
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y, H:i');
    }

    // ✅ Scope untuk unread notifications
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // ✅ Scope untuk specific user
    public function scopeForUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    // ✅ Scope untuk specific category
    public function scopeCategory($query, $category)
    {
        return $query->where('kategori', $category);
    }

    // ✅ Check if notification is expired
    public function getIsExpiredAttribute()
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    // ✅ Get badge class based on jenis
    public function getBadgeClassAttribute()
    {
        return match($this->jenis) {
            'success' => 'bg-success',
            'warning' => 'bg-warning',
            'danger' => 'bg-danger',
            'info' => 'bg-info',
            default => 'bg-primary'
        };
    }

    // ✅ Get icon based on kategori
    public function getIconAttribute()
    {
        return match($this->kategori) {
            'lamaran' => 'bi-file-earmark-text',
            'magang' => 'bi-briefcase',
            'sistem' => 'bi-gear',
            'pengumuman' => 'bi-megaphone',
            'evaluasi' => 'bi-clipboard-check',
            'deadline' => 'bi-alarm',
            default => 'bi-bell'
        };
    }
}