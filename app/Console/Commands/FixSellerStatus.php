<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SellerProfile;

class FixSellerStatus extends Command
{
    protected $signature = 'seller:fix-status {user_id?}';
    protected $description = 'Fix seller status for admin users';

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return;
            }
        } else {
            $user = User::where('role', 'admin')->first();
            if (!$user) {
                $this->error("No admin user found");
                return;
            }
        }

        $this->info("User: {$user->name} (ID: {$user->id}, Role: {$user->role})");

        $seller = $user->sellerProfile;
        if (!$seller) {
            $this->error("No seller profile found for this user");
            return;
        }

        $this->info("Current seller status: {$seller->status}");
        $this->info("Company: {$seller->company_name}");
        $this->info("Created: {$seller->created_at}");
        $this->info("Updated: {$seller->updated_at}");

        if ($seller->status !== 'approved') {
            if ($this->confirm("Do you want to approve this seller?")) {
                $seller->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ]);
                $this->info("Seller approved successfully!");
            }
        } else {
            $this->info("Seller is already approved");
        }

        // Clear any cache
        $this->call('cache:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        
        $this->info("Cache cleared. Try accessing /seller/dashboard again.");
    }
}