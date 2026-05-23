<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_out(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->format('Y-m-d'),
            'clock_in' => now()->subHours(8),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)
            ->post('/attendance/clock-out');

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'finished',
        ]);
    }

    public function test_user_cannot_clock_out_twice(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->format('Y-m-d'),
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
            'status' => 'finished',
        ]);

        $response = $this->actingAs($user)
            ->post('/attendance/clock-out');

        $response->assertRedirect();

        $response->assertSessionHas(
            'error',
            'すでに退勤済みです'
        );

        $this->assertEquals(
            1,
            Attendance::where('user_id', $user->id)->count()
        );
    }
}
