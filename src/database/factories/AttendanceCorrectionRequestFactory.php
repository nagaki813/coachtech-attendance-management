<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceCorrectionRequest>
 */
class AttendanceCorrectionRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requestedClockIn = Carbon::today()
            ->setTime(fake()->numberBetween(8, 10), fake()->randomElement([0, 15, 30, 45]));

        $requestedClockOut = (clone $requestedClockIn)
            ->addHours(fake()->numberBetween(7, 10))
            ->addMinutes(fake()->randomElement([0, 15, 30, 45]));

        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'approved']),
            'requested_clock_in' => $requestedClockIn,
            'requested_clock_out' => $requestedClockOut,
            'note' => fake()->randomElement([
                '出勤時間の打刻を忘れたため修正をお願いします。',
                '退勤時間に誤りがあるため修正をお願いします。',
                '休憩時間の記録に誤りがあるため修正をお願いします。',
            ]),
        ];
    }
}
