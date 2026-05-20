<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workDate = fake()->dateTimeBetween('-2 months', 'now');

        $clockIn = Carbon::instance($workDate)
            ->setTime(fake()->numberBetween(8, 10), fake()->randomElement([0, 15, 30, 45]));

        $clockOut = (clone $clockIn)
            ->addHours(fake()->numberBetween(7, 10))
            ->addMinutes(fake()->randomElement([0, 15, 30, 45]));

        return [
            'user_id' => User::factory(),
            'work_date' => $clockIn->format('Y-m-d'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'status' => 'finished',
            'note' => null,
        ];
    }
}
