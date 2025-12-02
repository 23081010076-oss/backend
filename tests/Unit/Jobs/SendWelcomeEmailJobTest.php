<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\SendWelcomeEmail;
use App\Mail\WelcomeEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class SendWelcomeEmailJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function welcome_email_job_can_be_dispatched()
    {
        Queue::fake();

        $user = User::factory()->create();

        SendWelcomeEmail::dispatch($user);

        Queue::assertPushed(SendWelcomeEmail::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
    }

    /** @test */
    public function welcome_email_job_is_pushed_to_emails_queue()
    {
        Queue::fake();

        $user = User::factory()->create();

        SendWelcomeEmail::dispatch($user);

        Queue::assertPushedOn('emails', SendWelcomeEmail::class);
    }

    /** @test */
    public function welcome_email_job_sends_email_when_handled()
    {
        Mail::fake();

        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        $job = new SendWelcomeEmail($user);
        $job->handle();

        Mail::assertSent(WelcomeEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function welcome_email_job_has_correct_retry_configuration()
    {
        $user = User::factory()->create();
        $job = new SendWelcomeEmail($user);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(60, $job->timeout);
        $this->assertEquals(30, $job->backoff);
    }
}
