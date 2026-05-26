<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCorrectionRequestListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_pending_correction_requests(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
            'name' => '山田 太郎',
        ]);

        $workDate = now()->format('Y-m-d');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
            'status' => 'finished',
        ]);

        AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => $workDate . ' 10:00:00',
            'requested_clock_out' => $workDate . ' 19:00:00',
            'status' => 'pending',
            'note' => '電車遅延のため',
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.correction-requests.index')
        );

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('承認待ち');
        $response->assertSee('電車遅延のため');
    }

    public function test_admin_can_view_approved_correction_requests(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
            'name' => '山田 太郎',
        ]);

        $workDate = now()->format('Y-m-d');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
            'status' => 'finished',
        ]);

        AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => $workDate . ' 10:00:00',
            'requested_clock_out' => $workDate . ' 19:00:00',
            'status' => 'approved',
            'note' => '電車遅延のため',
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.correction-requests.index', [
                'status' => 'approved',
            ])
        );

        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee('山田 太郎');
    }

    public function test_admin_can_view_correction_request_detail(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin'
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
            'name' => '山田 太郎',
        ]);

        $workDate = now()->format('Y-m-d');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
            'status' => 'finished',
        ]);

        $correctionRequest = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => $workDate . ' 10:00:00',
            'requested_clock_out' => $workDate . ' 19:00:00',
            'status' => 'pending',
            'note' => '電車遅延のため',
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.correction-requests.show', $correctionRequest->id)
        );

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
        $response->assertSee('電車遅延のため');
    }
}
