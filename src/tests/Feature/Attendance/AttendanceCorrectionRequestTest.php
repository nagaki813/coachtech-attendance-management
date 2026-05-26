<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_attendance_correction(): void
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
            'clock_out' => $workDate . ' 18:00:00',
        ]);

        $response = $this->actingAs($user)->post(
            route('attendance_correction_requests.store', $attendance->id),
            [
                'requested_clock_in' => '10:00',
                'requested_clock_out' => '19:00',
                'note' => '電車遅延のため',
            ]
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_correction_requests', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'requested_clock_in' => now()->format('Y-m-d') . ' 10:00:00',
            'requested_clock_out' => now()->format('Y-m-d') . ' 19:00:00',
            'status' => 'pending',
            'note' => '電車遅延のため',
        ]);
    }

    public function test_clock_in_must_be_before_clock_out(): void
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
            'clock_out' => $workDate . ' 18:00:00',
        ]);

        $response = $this->actingAs($user)->post(
            route('attendance_correction_requests.store', $attendance->id),
            [
                'requested_clock_in' => '20:00',
                'requested_clock_out' => '19:00',
                'note' => '修正理由です',
            ]
        );

        $response->assertSessionHasErrors([
            'requested_clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_note_is_required(): void
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
            'clock_out' => $workDate . ' 18:00:00',
        ]);

        $response = $this->actingAs($user)->post(
            route('attendance_correction_requests.store', $attendance->id),
            [
                'requested_clock_in' => '10:00',
                'requested_clock_out' => '19:00',
                'note' => '',
            ]
        );

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }

    public function test_pending_request_is_saved_and_shown_in_request_list(): void
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
            'clock_out' => $workDate . ' 18:00:00',
        ]);

        $this->actingAs($user)->post(
            route('attendance_correction_requests.store', $attendance->id),
            [
                'requested_clock_in' => '10:00',
                'requested_clock_out' => '19:00',
                'note' => '電車遅延のため',
            ]
        );

        $response = $this->actingAs($user)->get(
            route('attendance_correction_requests.index')
        );

        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        $response->assertSee('電車遅延のため');
    }

    public function test_break_start_must_be_within_work_time(): void
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
            'clock_out' => $workDate . ' 18:00:00',
        ]);

        $response = $this->actingAs($user)->post(
            route('attendance_correction_requests.store', $attendance->id),
            [
                'requested_clock_in' => '09:00',
                'requested_clock_out' => '18:00',
                'breaks' => [
                    [
                        'break_start' => '19:00',
                        'break_end' => '19:30',
                    ],
                ],
                'note' => '休憩時間修正のため',
            ]
        );

        $response->assertSessionHasErrors();

        $this->assertTrue(
            collect(session('errors')->getBag('default')->all())
                ->contains('休憩時間が不適切な値です')
        );
    }

    public function test_break_end_must_be_before_clock_out(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user'
        ]);

        $workDate = now()->format('Y-m-d');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
        ]);

        $response = $this->actingAs($user)->post(
            route('attendance_correction_requests.store', $attendance->id),
            [
                'requested_clock_in' => '09:00',
                'requested_clock_out' => '18:00',
                'breaks' => [
                    [
                        'break_start' => '12:00',
                        'break_end' => '19:00',
                    ],
                ],
                'note' => '休憩時間修正のため',
            ]
        );

        $response->assertSessionHasErrors();

        $this->assertTrue(
            collect(session('errors')->getBag('default')->all())
                ->contains('休憩時間もしくは退勤時間が不適切な値です')
        );
    }
}
