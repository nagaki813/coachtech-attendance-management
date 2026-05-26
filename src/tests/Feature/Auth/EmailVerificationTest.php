<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => '山田 太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_unverified_user_can_view_email_verification_notice(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get(
            route('verification.notice')
        );

        $response->assertStatus(200);
        $response->assertSee('認証メールを再送信する');
    }

    public function test_user_is_redirected_after_email_verification(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('attendances.index'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
