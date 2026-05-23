<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_date_and_time_are_displayed(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(
            route('attendances.index')
        );

        $response->assertStatus(200);

        $response->assertSee(now()->format('Y年n月j日'));
    }

    public function test_current_day_of_week_is_displayed(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $weekDays = [
            'Sunday' => '日',
            'Monday' => '月',
            'Tuesday' => '火',
            'Wednesday' => '水',
            'Thursday' => '木',
            'Friday' => '金',
            'Saturday' => '土',
        ];

        $today = now()->englishDayOfWeek;

        $response = $this->actingAs($user)->get(
            route('attendances.index')
        );

        $response->assertStatus(200);

        $response->assertSee($weekDays[$today]);
    }

    public function test_status_is_off_duty_before_clock_in(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(
            route('attendances.index')
        );

        $response->assertStatus(200);

        $response->assertSee('勤務外');
    }

    public function test_status_is_working_after_clock_in(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $workDate = now()->format('Y-m-d');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => null,
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)->get(
            route('attendances.index')
        );

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    public function test_status_is_on_break(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $workDate = now()->format('Y-m-d');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => null,
            'status' => 'on_break',
        ]);

        $response = $this->actingAs($user)->get(
            route('attendances.index')
        );

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    public function test_status_is_finished_after_clock_out(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $workDate = now()->format('Y-m-d');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
            'status' => 'finished',
        ]);

        $response = $this->actingAs($user)->get(
            route('attendances.index')
        );

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
