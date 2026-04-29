<?php

    namespace App\Livewire;

    use Livewire\Component;
    use App\Models\Medicine;
    use App\Models\MedicineBatch;
    use Illuminate\Support\Facades\DB;

    class MedicineBatches extends Component
    {
        public Medicine $medicine;

        // ─── Add batch fields ─────────────────────────────────────────
        public $newBatchNumber      = '';
        public $newBatchQty         = '';
        public $newBatchExpiry      = '';
        public $newBatchManufactured= '';

        // ─── Edit batch fields ────────────────────────────────────────
        public $editBatchId         = null;
        public $editBatchNumber     = '';
        public $editBatchQty        = '';
        public $editBatchExpiry     = '';
        public $editBatchManufactured = '';

        // ─── Archive state ────────────────────────────────────────────
        public $archiveBatchId      = null;
        public $showArchived        = false;

        // ─── Mount ───────────────────────────────────────────────────

        public function mount(Medicine $medicine): void
        {
            $this->medicine      = $medicine;
        }

        // ─── Expiry status helper ─────────────────────────────────────

        private function determineExpiryStatus($expiry_date): string
        {
            $days = now()->diffInDays($expiry_date, false);
            if ($days < 0)   return 'Expired';
            if ($days <= 30) return 'Expiring Soon';
            return 'Valid';
        }

        private function determineStockStatus($stock): string
        {
            if ($stock <= 0)  return 'Out of Stock';
            if ($stock <= 10) return 'Low Stock';
            return 'In Stock';
        }

        // ─── Add batch ────────────────────────────────────────────────

        public function addBatch(): void
        {
            $this->validate([
                'newBatchQty'         => 'required|integer|min:1',
                'newBatchExpiry'      => 'required|date|after:today',
                'newBatchNumber'      => 'nullable|string|max:100',
                'newBatchManufactured'=> 'nullable|date',
            ], [
                'newBatchQty.required'    => 'Quantity is required.',
                'newBatchExpiry.required' => 'Expiry date is required.',
                'newBatchExpiry.after'    => 'Expiry date must be in the future.',
            ]);

            $expiryStatus = $this->determineExpiryStatus($this->newBatchExpiry);

            DB::transaction(function () use ($expiryStatus) {
                MedicineBatch::create([
                    'medicine_id'       => $this->medicine->medicine_id,
                    'batch_number'      => $this->newBatchNumber ?: 'BATCH-' . strtoupper(uniqid()),
                    'quantity'          => (int) $this->newBatchQty,
                    'initial_quantity'  => (int) $this->newBatchQty,
                    'manufactured_date' => $this->newBatchManufactured ?: null,
                    'expiry_date'       => $this->newBatchExpiry,
                    'expiry_status'     => $expiryStatus,
                ]);

                // Update parent medicine aggregate stock
                $newStock = $this->medicine->stock + (int) $this->newBatchQty;
                $this->medicine->update([
                    'stock'         => $newStock,
                    'stock_status'  => $this->determineStockStatus($newStock),
                    'expiry_date'   => $this->newBatchExpiry,
                    'expiry_status' => $expiryStatus,
                ]);

                // Refresh medicine model
                $this->medicine->refresh();
            });

            $this->reset(['newBatchNumber', 'newBatchQty', 'newBatchExpiry', 'newBatchManufactured']);

            session()->flash('batch_success', 'Batch added successfully.');
        }

        // ─── Edit batch ───────────────────────────────────────────────

        public function editBatch($batchId): void
        {
            $batch = MedicineBatch::findOrFail($batchId);

            $this->editBatchId           = $batchId;
            $this->editBatchNumber       = $batch->batch_number;
            $this->editBatchQty          = $batch->quantity;
            $this->editBatchExpiry       = $batch->expiry_date->format('Y-m-d');
            $this->editBatchManufactured = $batch->manufactured_date
                ? $batch->manufactured_date->format('Y-m-d')
                : '';

            $this->dispatch('show-edit-batch-modal');
        }

    public function updateBatch(): void
    {
        $this->validate([
            'editBatchNumber'       => 'nullable|string|max:100',
            'editBatchQty'          => 'required|integer|min:0',
            'editBatchExpiry'       => 'required|date',
            'editBatchManufactured' => 'nullable|date',
        ], [
            'editBatchQty.required'    => 'Quantity is required.',
            'editBatchExpiry.required' => 'Expiry date is required.',
        ]);

        $batch        = MedicineBatch::findOrFail($this->editBatchId);
        $oldQty       = $batch->quantity;
        $expiryStatus = $this->determineExpiryStatus($this->editBatchExpiry);
        $newQty       = (int) $this->editBatchQty;

        DB::transaction(function () use ($batch, $oldQty, $expiryStatus, $newQty) {
            $batch->update([
                'batch_number'      => $this->editBatchNumber ?: $batch->batch_number,
                'quantity'          => $newQty,
                // initial_quantity is intentionally NOT updated here.
                // It is set once on creation and represents the original
                // stock received. Editing remaining qty must not alter it.
                'manufactured_date' => $this->editBatchManufactured ?: null,
                'expiry_date'       => $this->editBatchExpiry,
                'expiry_status'     => $expiryStatus,
            ]);

            $qtyDiff  = $newQty - $oldQty;
            $newStock = max(0, $this->medicine->stock + $qtyDiff);
            $this->medicine->update([
                'stock'        => $newStock,
                'stock_status' => $this->determineStockStatus($newStock),
            ]);

            $this->medicine->refresh();
        });

        $this->resetEditFields();
        $this->dispatch('close-edit-batch-modal');
        session()->flash('batch_success', 'Batch updated successfully.');
    }

        public function cancelEdit(): void
        {
            $this->resetEditFields();
            $this->dispatch('close-edit-batch-modal');
        }

        private function resetEditFields(): void
        {
            $this->editBatchId           = null;
            $this->editBatchNumber       = '';
            $this->editBatchQty          = '';
            $this->editBatchExpiry       = '';
            $this->editBatchManufactured = '';
            $this->resetErrorBag();
        }

        // ─── Archive / restore batch ──────────────────────────────────

        public function confirmArchiveBatch($batchId): void
        {
            $this->archiveBatchId = $batchId;
            $this->dispatch('show-archive-batch-confirmation');
        }

        public function archiveBatch(): void
        {
            $batch    = MedicineBatch::findOrFail($this->archiveBatchId);
            $archivedQty = $batch->quantity;

            DB::transaction(function () use ($batch, $archivedQty) {
                $batch->delete();

                // Deduct archived qty from medicine stock
                $newStock = max(0, $this->medicine->stock - $archivedQty);
                $this->medicine->update([
                    'stock'        => $newStock,
                    'stock_status' => $this->determineStockStatus($newStock),
                ]);

                $this->medicine->refresh();
            });

            $this->archiveBatchId = null;
            session()->flash('batch_success', 'Batch archived successfully.');
        }

        public function restoreBatch($batchId): void
        {
            $batch = MedicineBatch::withTrashed()->findOrFail($batchId);

            DB::transaction(function () use ($batch) {
                $batch->restore();

                $newStock = $this->medicine->stock + $batch->quantity;
                $this->medicine->update([
                    'stock'        => $newStock,
                    'stock_status' => $this->determineStockStatus($newStock),
                ]);

                $this->medicine->refresh();
            });

            session()->flash('batch_success', 'Batch restored successfully.');
        }

        public function toggleArchived(): void
        {
            $this->showArchived = !$this->showArchived;
        }

        // ─── Render ───────────────────────────────────────────────────

public function render()
{
    $batchQuery = MedicineBatch::where('medicine_id', $this->medicine->medicine_id)
        ->orderBy('expiry_date', 'asc');

    if ($this->showArchived) {
        $batchQuery->onlyTrashed();
    }

    $batches = $batchQuery->get();

    // ── Cost summaries (always from non-trashed batches) ──
    $allActiveBatches = MedicineBatch::where('medicine_id', $this->medicine->medicine_id)->get();


    return view('livewire.medicine-batches', [
        'batches'           => $batches,
    ])->layout('livewire.layouts.base', ['page' => 'BATCH MANAGEMENT']);
}
    }