<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestPplData;

class NotificationAndSecurityTest extends TestCase
{
    use RefreshDatabase, CreatesTestPplData;

    protected User $user;
    protected Notifikasi $notif;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->createTestPplData();

        $this->user = $this->ketuaUser;
        $this->notif = Notifikasi::create([
            'user_id' => $this->user->id,
            'judul' => 'Notifikasi Pengujian Security',
            'pesan' => 'Pesan pengujian sistem notifikasi.',
            'tipe' => 'sistem',
            'link' => '/ketua/logbook',
            'is_read' => false,
        ]);
    }

    public function test_user_can_see_unread_notifications_in_header(): void
    {
        $response = $this->actingAs($this->user)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Notifikasi Pengujian Security');
    }

    public function test_user_can_mark_notification_as_read_and_redirect_to_link(): void
    {
        $response = $this->actingAs($this->user)->post("/notifikasi/{$this->notif->id}/read");

        $response->assertRedirect($this->notif->link);
        $this->assertDatabaseHas('notifikasi', [
            'id' => $this->notif->id,
            'is_read' => true,
        ]);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $response = $this->actingAs($this->user)->post('/notifikasi/read-all');

        $response->assertRedirect();
        $this->assertDatabaseMissing('notifikasi', [
            'user_id' => $this->user->id,
            'is_read' => false,
        ]);
    }

    public function test_user_cannot_mark_other_users_notification(): void
    {
        $otherUser = User::where('id', '!=', $this->user->id)->first();

        $response = $this->actingAs($otherUser)->post("/notifikasi/{$this->notif->id}/read");

        $response->assertStatus(403);
    }
}
