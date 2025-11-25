<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Log;

class CleanupExpiredVerificationCodes extends Command
{
    protected $signature = 'verification-codes:cleanup';
    protected $description = 'Remove expired verification codes from database';

    public function handle()
    {
        $deletedCount = VerificationCode::where('expires_at', '<', now())->delete();
        
        Log::info("Cleaned up {$deletedCount} expired verification codes");
        $this->info("Cleaned up {$deletedCount} expired verification codes");
        
        return Command::SUCCESS;
    }
}