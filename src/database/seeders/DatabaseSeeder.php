<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionRequestBreak;
use Carbon\Carbon;
use Database\Seeders\AdminUserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $users = User::factory(5)->create();

        $months = [
            Carbon::now()->subMonths(2),
            Carbon::now()->subMonth(),
            Carbon::now(),
        ];

        foreach ($users as $user) {
            foreach ($months as $month) {
                for ($day = 1; $day <= 20; $day++) {
                    $workDate = $month->copy()
                        ->startOfDay()
                        ->addDays($day - 1);

                    if ($workDate->isFuture()) {
                        continue;
                    }

                    if ($workDate->isWednesday() || $workDate->isSunday()) {
                        continue;
                    }

                    $clockIn = $workDate->copy()
                        ->setTime(rand(8, 10), collect([0, 15, 30, 45])->random());

                    $clockOut = $clockIn->copy()
                        ->addHours(rand(7, 10))
                        ->addMinutes(collect([0, 15, 30, 45])->random());

                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'work_date' => $workDate->format('Y-m-d'),
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'status' => 'finished',
                    ]);

                    $breakStart = $clockIn->copy()->addHours(4);

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $breakStart,
                        'break_end' => $breakStart->copy()->addMinutes(60),
                    ]);

                    if (rand(1, 3) === 1) {
                        $correctionRequest = AttendanceCorrectionRequest::create([
                            'attendance_id' => $attendance->id,
                            'user_id' => $user->id,
                            'status' => collect(['pending', 'approved'])->random(),
                            'requested_clock_in' => $clockIn->copy()->addMinutes(15),
                            'requested_clock_out' => $clockOut->copy()->addMinutes(15),
                            'note' => '打刻漏れのため修正申請',
                        ]);

                        AttendanceCorrectionRequestBreak::create([
                            'correction_request_id' => $correctionRequest->id,
                            'break_start' => (clone $breakStart)->addMinutes(15),
                            'break_end' => (clone $breakStart)
                                ->addMinutes(75),
                        ]);
                    }
                }
            }
        }
    }
}
