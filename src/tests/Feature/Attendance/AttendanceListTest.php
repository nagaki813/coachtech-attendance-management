<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_attendance_list(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::create(2026, 5, 10),
            'clock_in' => Carbon::create(2026, 5, 10, 9, 0),
            'clock_out' => Carbon::create(2026, 5, 10, 18, 0),
            'status' => 'finished',
        ]);

        $response = $this->actingAs($user)
            ->get('/attendance/list?month=2026-05');

        $response->assertStatus(200);

        $response->assertSee('05/10');
    }

    public function test_attendance_list_displays_all_days_of_month(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->get('/attendance/list?month=2026-05');

        $response->assertStatus(200);

        $response->assertSee('05/01');
        $response->assertSee('05/31');
    }

    public function test_attendance_list_displays_blank_days(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::create(2026, 5, 10),
            'clock_in' => Carbon::create(2026, 5, 10, 9, 0),
            'clock_out' => Carbon::create(2026, 5, 10, 18, 0),
            'status' => 'finished',
        ]);

        $response = $this->actingAs($user)
            ->get('/attendance/list?month=2026-05');

        $response->assertStatus(200);

        $response->assertSee('05/09');
        $response->assertSee('05/11');
    }
}
