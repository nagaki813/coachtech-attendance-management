<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_break(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->format('Y-m-d'),
            'clock_in' => now(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)
            ->post('/attendance/break/start');

        $response->assertRedirect();

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'on_break',
        ]);
    }

    public function test_user_can_end_break(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->format('Y-m-d'),
            'clock_in' => now(),
            'status' => 'on_break',
        ]);

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subHour(),
            'break_end' => null,
        ]);

        $response = $this->actingAs($user)
            ->post('/attendance/break/end');

        $response->assertRedirect();

        $this->assertDatabaseMissing('breaks', [
            'id' => $break->id,
            'break_end' => null,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'working',
        ]);
    }
}
