<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Mail\InventoryDailyDigestMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TestInventoryDigest extends Command
{
    protected $signature = 'test:inventory-digest
                            {--email= : Override recipient email}
                            {--fake   : Use fake/stub data instead of real DB records}
                            {--empty  : Send an all-clear digest with no flagged items}';

    protected $description = 'Send a test daily inventory digest email';

    public function handle(): void
    {
        $recipient = $this->resolveRecipient();
        if (! $recipient) return;

        [$outOfStock, $lowStock, $expiringSoon, $expired] = match(true) {
            $this->option('empty') => $this->emptyData(),
            $this->option('fake')  => $this->fakeData(),
            default                => $this->realData(),
        };

        $windowStart = Carbon::today('Asia/Manila')->subDay()->setTime(8, 0, 0);
        $windowEnd   = Carbon::today('Asia/Manila')->setTime(8, 0, 0);

        $toEmail = $this->option('email') ?? $recipient->email;

        Mail::to($toEmail)->send(new InventoryDailyDigestMail(
            $recipient,
            $outOfStock,
            $lowStock,
            $expiringSoon,
            $expired,
            $windowStart,
            $windowEnd,
        ));

        $this->info("✅ Digest sent to {$toEmail}");
        $this->table(
            ['Section', 'Count'],
            [
                ['Out of Stock',  $outOfStock->count()],
                ['Low Stock',     $lowStock->count()],
                ['Expiring Soon', $expiringSoon->count()],
                ['Expired',       $expired->count()],
            ]
        );
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function resolveRecipient(): ?User
    {
        $user = User::whereIn('role', ['nurse', 'staff'])->first();

        if (! $user) {
            $this->error('No nurse/staff user found in the database.');
            return null;
        }

        return $user;
    }

    private function emptyData(): array
    {
        $this->info('Using EMPTY data — simulates all-clear digest (nothing flagged in last 24 h).');

        return [
            collect(),
            collect(),
            collect(),
            collect(),
        ];
    }

    private function realData(): array
    {
        $windowStart = Carbon::today('Asia/Manila')->subDay()->setTime(8, 0, 0);
        $windowEnd   = Carbon::today('Asia/Manila')->setTime(8, 0, 0);

        $outOfStock = Medicine::whereNull('deleted_at')
            ->where('stock', '<=', 0)
            ->with('category')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        $lowStock = Medicine::whereNull('deleted_at')
            ->where('stock', '>', 0)->where('stock', '<=', 10)
            ->with('category')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        $expiringSoon = MedicineBatch::with('medicine.category')
            ->whereNull('deleted_at')
            ->where('expiry_status', 'Expiring Soon')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        $expired = MedicineBatch::with('medicine.category')
            ->whereNull('deleted_at')
            ->where('expiry_status', 'Expired')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        $this->warn('Using REAL data from DB (time window: last 24 h).');
        $this->warn('If counts are all 0, use --fake to verify the email template instead.');

        return [$outOfStock, $lowStock, $expiringSoon, $expired];
    }

    private function fakeData(): array
    {
        $med = (object)[
            'medicine_name' => 'Amoxicillin',
            'dosage'        => '500 mg',
            'stock'         => 0,
            'stock_status'  => 'Out of Stock',
            'category'      => (object)['category_name' => 'Antibiotic'],
        ];

        $med2 = (object)[
            'medicine_name' => 'Paracetamol',
            'dosage'        => '250 mg',
            'stock'         => 4,
            'stock_status'  => 'Low Stock',
            'category'      => (object)['category_name' => 'Analgesic'],
        ];

        $batch = (object)[
            'batch_number' => 'BATCH-2024-001',
            'expiry_date'  => Carbon::now()->addDays(12),
            'expiry_status'=> 'Expiring Soon',
            'medicine'     => (object)[
                'medicine_name' => 'Metformin',
                'dosage'        => '500 mg',
                'category'      => (object)['category_name' => 'Antidiabetic'],
            ],
        ];

        $batch2 = (object)[
            'batch_number' => 'BATCH-2023-099',
            'expiry_date'  => Carbon::now()->subDays(3),
            'expiry_status'=> 'Expired',
            'medicine'     => (object)[
                'medicine_name' => 'Cetirizine',
                'dosage'        => '10 mg',
                'category'      => (object)['category_name' => 'Antihistamine'],
            ],
        ];

        $this->info('Using FAKE stub data — no DB queries.');

        return [
            collect([$med]),
            collect([$med2]),
            collect([$batch]),
            collect([$batch2]),
        ];
    }
}