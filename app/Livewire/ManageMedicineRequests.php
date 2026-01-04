<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicineRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicineRequestLog;

class ManageMedicineRequests extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterStatus = 'pending';
    public $perPage = 10;

    // Property for view details
    public $viewRequest;

    private function determineStockStatus($stock)
    {
        if ($stock <= 0) {
            return 'Out of Stock';
        }
        if ($stock <= 10) {
            return 'Low Stock';
        }
        return 'In Stock';
    }

    public function approve($requestId)
    {
        DB::transaction(function () use ($requestId) {

            $request = MedicineRequest::with(['medicine', 'patients'])
                ->lockForUpdate()
                ->findOrFail($requestId);

            if ($request->status !== 'pending') {
                session()->flash('error', 'This request has already been processed.');
                return;
            }

            if ($request->medicine->stock < $request->quantity_requested) {
                session()->flash('error', 'Insufficient medicine stock.');
                return;
            }

            // deduct stock
            $request->medicine->decrement('stock', $request->quantity_requested);

            // recalculate and update stock status
            $newStock = $request->medicine->fresh()->stock;
            $newStockStatus = $this->determineStockStatus($newStock);

            $request->medicine->update([
                'stock_status' => $newStockStatus
            ]);

            // update status
            $request->update([
                'status' => 'completed',
            ]);

            // create log for APPROVAL
            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->patients->full_name,
                'medicine_name'       => $request->medicine->medicine_name,
                'dosage'              => $request->medicine->dosage,
                'quantity'            => $request->quantity_requested,
                'action'              => 'approved',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username,
                'performed_at'        => now(),
            ]);
        });
        $this->dispatch('approve-modal');
        session()->flash('message', 'Medicine request approved successfully.');
    }

    public function reject($requestId)
    {
        DB::transaction(function () use ($requestId) {
            $request = MedicineRequest::with(['medicine', 'patients'])
                ->lockForUpdate()
                ->findOrFail($requestId);

            if ($request->status !== 'pending') {
                session()->flash('error', 'This request has already been processed.');
                return;
            }

            // update status
            $request->update([
                'status' => 'rejected',
            ]);

            // create log for REJECTION
            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->patients->full_name,
                'medicine_name'       => $request->medicine->medicine_name,
                'dosage'              => $request->medicine->dosage,
                'quantity'            => $request->quantity_requested,
                'action'              => 'rejected',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username,
                'performed_at'        => now(),
            ]);
        });

        session()->flash('message', 'Medicine request rejected.');
    }

    public function viewDetails($requestId)
    {
        $this->viewRequest = MedicineRequest::with(['medicine', 'patients'])
            ->findOrFail($requestId);
    }

    public function getPendingCount(){
        return MedicineRequest::where('status', 'pending')->count();
    }

    public function getCompletedCount(){
        return MedicineRequest::where('status', 'completed')->count();
    }

    public function getRejectedCount(){
        return MedicineRequest::where('status', 'rejected')->count();
    }

    public function getTotalCount(){
        return MedicineRequest::count();
    }

    public function render()
    {
        $requests = MedicineRequest::with(['medicine', 'patients'])
            ->when($this->search, function ($q) {
                $q->whereHas('patients', fn ($p) =>
                        $p->where('full_name', 'like', "%{$this->search}%")
                    )
                  ->orWhereHas('medicine', fn ($m) =>
                        $m->where('medicine_name', 'like', "%{$this->search}%")
                    );
            })
            ->when($this->filterStatus, fn ($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.manage-medicine-requests', compact('requests'))
            ->layout('livewire.layouts.base');
    }
}