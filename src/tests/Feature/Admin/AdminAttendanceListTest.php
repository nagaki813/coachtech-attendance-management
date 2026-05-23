<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_attendance_list_for_specific_date(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
            'name' => '山田 太郎',
        ]);

        $workDate = now()->format('Y-m-d');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
            'status' => 'finished',
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.attendances.index', ['date' => $workDate])
        );

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_admin_can_view_previous_date_attendance(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
            'name' => '山田 太郎',
        ]);

        $workDate = now()->subDay()->format('Y-m-d');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
            'status' => 'finished',
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.attendances.index', ['date' => $workDate])
        );

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
    }
}
