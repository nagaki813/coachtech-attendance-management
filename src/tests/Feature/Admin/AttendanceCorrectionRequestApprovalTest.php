<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionRequestApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_correction_request(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $workDate = now()->format('Y-m-d');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in' => $workDate . ' 09:00:00',
            'clock_out' => $workDate . ' 18:00:00',
        ]);

        $correctionRequest = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => $workDate . ' 10:00:00',
            'requested_clock_out' => $workDate . ' 19:00:00',
            'status' => 'pending',
            'note' => '電車遅延のため',
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.correction-requests.approve', $correctionRequest->id)
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => $workDate . ' 10:00:00',
            'clock_out' => $workDate . ' 19:00:00',
        ]);

        $this->assertDatabaseHas('attendance_correction_requests', [
            'id' => $correctionRequest->id,
            'status' => 'approved',
        ]);
    }
}
