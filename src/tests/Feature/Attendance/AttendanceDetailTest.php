<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_attendance_detail(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::create(2026, 5, 10),
            'clock_in' => Carbon::create(2026, 5, 10, 9, 0),
            'clock_out' => Carbon::create(2026, 5, 10, 18, 0),
            'status' => 'finished',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::create(2026, 5, 10, 12, 0),
            'break_end' => Carbon::create(2026, 5, 10, 13, 0),
        ]);

        $response = $this->actingAs($user)
            ->get("/attendance/{$attendance->id}");

        $response->assertStatus(200);

        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }

    public function test_user_cannot_view_other_users_attendance_detail(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $otherUser = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::create([
            'user_id' => $otherUser->id,
            'work_date' => now()->format('Y-m-d'),
            'clock_in' => now(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)
            ->get("/attendance/{$attendance->id}");

        $response->assertStatus(403);
    }
}
