<?php

namespace Tests\Unit\Models;

use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiModelTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /** @test */
    public function notifikasi_menggunakan_table_dan_primary_key_yang_benar()
    {
        $notif = new Notifikasi();

        $this->assertEquals('m_notifikasi', $notif->getTable());
        $this->assertEquals('id_notifikasi', $notif->getKeyName());
    }

    /** @test */
    public function data_terkait_di_cast_ke_array()
    {
        $notif = Notifikasi::factory()->create([
            'data_terkait' => ['id' => 123, 'type' => 'lamaran'],
        ]);

        $this->assertIsArray($notif->data_terkait);
        $this->assertEquals(123, $notif->data_terkait['id']);
    }

    /** @test */
    public function boolean_cast_berfungsi_dengan_benar()
    {
        $notif = Notifikasi::factory()->create([
            'is_read' => 1,
            'is_important' => 0,
        ]);

        $this->assertTrue($notif->is_read);
        $this->assertFalse($notif->is_important);
    }

    /** @test */
    public function time_ago_accessor_berfungsi()
    {
        Carbon::setTestNow(now());

        $notif = Notifikasi::factory()->create([
            'created_at' => now()->subMinutes(5),
        ]);

        $this->assertStringContainsString('minutes', $notif->time_ago);
    }

    /** @test */
    public function formatted_date_accessor_berfungsi()
    {
        $date = Carbon::create(2025, 1, 1, 10, 30);

        $notif = Notifikasi::factory()->create([
            'created_at' => $date,
        ]);

        $this->assertEquals('01 Jan 2025, 10:30', $notif->formatted_date);
    }

    /** @test */
    public function is_expired_true_jika_sudah_lewat()
    {
        $notif = Notifikasi::factory()->create([
            'expired_at' => now()->subDay(),
        ]);

        $this->assertTrue($notif->is_expired);
    }

    /** @test */
    public function is_expired_false_jika_belum_atau_null()
    {
        $notif = Notifikasi::factory()->create([
            'expired_at' => now()->addDay(),
        ]);

        $this->assertFalse($notif->is_expired);

        $notifNull = Notifikasi::factory()->create([
            'expired_at' => null,
        ]);

        $this->assertFalse($notifNull->is_expired);
    }

    /** @test */
    public function badge_class_ditentukan_berdasarkan_jenis()
    {
        $notif = Notifikasi::factory()->create([
            'jenis' => 'warning',
        ]);

        $this->assertEquals('bg-warning', $notif->badge_class);
    }

    /** @test */
    public function icon_ditentukan_berdasarkan_kategori()
    {
        $notif = Notifikasi::factory()->create([
            'kategori' => 'magang',
        ]);

        $this->assertEquals('bi-briefcase', $notif->icon);
    }

    /** @test */
    public function scope_unread_hanya_mengambil_notifikasi_belum_dibaca()
    {
        Notifikasi::factory()->count(2)->create(['is_read' => false]);
        Notifikasi::factory()->count(1)->create(['is_read' => true]);

        $this->assertCount(2, Notifikasi::unread()->get());
    }

    /** @test */
    public function scope_for_user_berfungsi()
    {
        $user = User::factory()->create();

        Notifikasi::factory()->count(3)->create([
            'id_user' => $user->id_user,
        ]);

        Notifikasi::factory()->count(2)->create();

        $this->assertCount(3, Notifikasi::forUser($user->id_user)->get());
    }

    /** @test */
    public function scope_category_berfungsi()
    {
        Notifikasi::factory()->count(2)->create([
            'kategori' => 'sistem',
        ]);

        Notifikasi::factory()->count(1)->create([
            'kategori' => 'magang',
        ]);

        $this->assertCount(2, Notifikasi::category('sistem')->get());
    }

    /** @test */
    public function notifikasi_memiliki_relasi_user()
    {
        $user = User::factory()->create();

        $notif = Notifikasi::factory()->create([
            'id_user' => $user->id_user,
        ]);

        $this->assertInstanceOf(User::class, $notif->user);
    }
}
