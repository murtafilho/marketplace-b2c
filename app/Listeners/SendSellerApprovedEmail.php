<?php

namespace App\Listeners;

use App\Events\SellerApproved;
use App\Mail\SellerApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendSellerApprovedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(SellerApproved $event): void
    {
        try {
            Mail::to($event->seller->email)
                ->send(new SellerApprovedMail($event->seller));
                
            Log::info('Seller approved email sent successfully', [
                'user_id' => $event->seller->id,
                'email' => $event->seller->email,
                'company_name' => $event->seller->sellerProfile->company_name ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send seller approved email', [
                'user_id' => $event->seller->id,
                'email' => $event->seller->email,
                'error' => $e->getMessage()
            ]);
            
            // Re-throw to retry the job
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(SellerApproved $event, \Throwable $exception): void
    {
        Log::error('Seller approved email job failed permanently', [
            'user_id' => $event->seller->id,
            'email' => $event->seller->email,
            'error' => $exception->getMessage()
        ]);
    }
}