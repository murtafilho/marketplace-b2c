<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\WelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        try {
            Mail::to($event->user->email)
                ->send(new WelcomeMail($event->user));
                
            Log::info('Welcome email sent successfully', [
                'user_id' => $event->user->id,
                'email' => $event->user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'error' => $e->getMessage()
            ]);
            
            // Re-throw to retry the job
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(UserRegistered $event, \Throwable $exception): void
    {
        Log::error('Welcome email job failed permanently', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'error' => $exception->getMessage()
        ]);
    }
}