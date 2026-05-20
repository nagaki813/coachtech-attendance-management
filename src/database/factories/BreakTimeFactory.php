<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BreakTime>
 */
class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $breakStart = Carbon::today()->setTime(12, 0);

        $breakStart->addMinutes(
            fake()->randomElement([0, 15, 30, 45])
        );

        $breakEnd = (clone $breakStart)->addMinutes(
            fake()->randomElement([30, 45, 60])
        );

        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ];
    }
}
