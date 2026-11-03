<?php

namespace App\Backend\Livewire\Gista;

use App\Backend\Models\PaymentDocument;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('frontend.layouts.gista')]
class PaymentList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'All';
    public bool $showAdvancedSearch = false;
    public bool $onlyIncomplete = false;

    // Advanced search inputs
    public string $advOptg = '';
    public string $advKeterangan = '';
    public string $advVendor = '';
    public string $advPo = '';
    public string $advStartDate = '';
    public string $advEndDate = '';

    public function mount(): void
    {
        // Menu sidebar "Dokumen Belum Lengkap" mengirim ?filter=incomplete.
        $this->onlyIncomplete = request('filter') === 'incomplete';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        // Pilih tab status berarti keluar dari mode "belum lengkap".
        $this->onlyIncomplete = false;
        $this->resetPage();
    }

    public function clearIncompleteFilter(): void
    {
        $this->onlyIncomplete = false;
        $this->resetPage();
    }

    public function resetAdvancedSearch(): void    {
        $this->advOptg = '';
        $this->advKeterangan = '';
        $this->advVendor = '';
        $this->advPo = '';
        $this->advStartDate = '';
        $this->advEndDate = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = PaymentDocument::query()
            ->visibleTo(auth()->user())
            ->latest();

        if ($this->onlyIncomplete) {
            $query->where('document_completeness', 'Incomplete');
        }

        if ($this->statusFilter === 'Paid') {
            $query->where('payment_status', 'Paid');
        } elseif ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('optg_number', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('po_number', 'like', "%{$this->search}%")
                  ->orWhere('vendor_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->advOptg) {
            $query->where('optg_number', 'like', "%{$this->advOptg}%");
        }
        if ($this->advKeterangan) {
            $query->where('description', 'like', "%{$this->advKeterangan}%");
        }
        if ($this->advVendor) {
            $query->where('vendor_name', 'like', "%{$this->advVendor}%");
        }
        if ($this->advPo) {
            $query->where('po_number', 'like', "%{$this->advPo}%");
        }
        if ($this->advStartDate) {
            $query->whereDate('created_at', '>=', $this->advStartDate);
        }
        if ($this->advEndDate) {
            $query->whereDate('created_at', '<=', $this->advEndDate);
        }

        $documents = $query->paginate(10);

        return view('frontend.gista.payment-list', [
            'documents' => $documents,
        ]);
    }
}
