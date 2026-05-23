<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_in(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->post('/attendance/clock-in');

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => 'working',
        ]);
    }

    public function test_user_cannot_clock_in_twice(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->format('Y-m-d'),
            'clock_in' => now(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)
            ->post('/attendance/clock-in');

        $response->assertRedirect();
        $response->assertSessionHas('error', 'すでに出勤済みです');

        $this->assertEquals(
            1,
            Attendance::where('user_id', $user->id)->count()
        );
    }
}
