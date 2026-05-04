<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicineBatch;

class RecalculateBatchExpiryStatuses extends Command
{
    protected $signature   = 'batches:recalculate-expiry';
    protected $description = 'Recalculate expiry_status for all active medicine batches';

    public function handle(): void
    {
        $batches = MedicineBatch::withoutTrashed()->get();
        $batches->each->recalculateExpiryStatus();
        $this->info("Updated {$batches->count()} batches.");
    }
}