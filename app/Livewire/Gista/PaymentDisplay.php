<?php

namespace App\Livewire\Gista;

use App\Models\PaymentDocument;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.gista')]
class PaymentDisplay extends Component
{
    public PaymentDocument $document;

    // Modals
    public bool $showWorkflowModal = false;
    public bool $showFakturModal = false;
    public bool $showEsgModal = false;
    public bool $showJurnalModal = false;

    // Approval / Rejection actions
    public bool $showActionModal = false;
    public string $actionType = ''; // approve, reject, pending
    public string $actionNotes = '';
    public string $completenessStatus = 'Complete'; // Complete or Incomplete (for SPV 2)

    public function mount(PaymentDocument $document): void
    {
        $this->document = $document->load(['items', 'taxInvoices', 'additionalCosts', 'attachments', 'workflowLogs', 'allocations', 'esgRecords']);
    }

    public function openAction(string $type): void
    {
        $this->actionType = $type;
        $this->actionNotes = '';
        $this->showActionModal = true;
    }

    public function confirmAction(): void
    {
        $user = auth()->user();

        if ($this->actionType === 'reject') {
            $this->document->update([
                'status' => 'Correction',
                'last_modified_by' => $user->nip . ' - ' . $user->name,
            ]);
            $this->document->logWorkflow('Correction', $this->actionNotes ?: 'Dokumen dikembalikan untuk perbaikan');
            session()->flash('success', 'Dokumen berhasil di-reject (status: Correction).');
        } elseif ($this->actionType === 'pending') {
            $this->document->update([
                'status' => 'Pending',
                'last_modified_by' => $user->nip . ' - ' . $user->name,
            ]);
            $this->document->logWorkflow('Pending', $this->actionNotes ?: 'Dokumen dipending oleh tim payment');
            session()->flash('success', 'Dokumen berhasil di-pending.');
        } elseif ($this->actionType === 'approve') {
            if ($this->document->status === 'Waiting for Approval 1' || $this->document->status === 'Waiting for Checker') {
                $this->document->update([
                    'status' => 'Waiting for Approval 2',
                    'last_modified_by' => $user->nip . ' - ' . $user->name,
                ]);
                $this->document->logWorkflow('Waiting for Approval 2', $this->actionNotes ?: 'Disetujui Supervisor 1, diteruskan ke Biro Payment');
                session()->flash('success', 'Dokumen disetujui dan diteruskan ke Approval 2 (Biro Payment).');
            } elseif ($this->document->status === 'Waiting for Approval 2') {
                $completeness = ($this->completenessStatus === 'Incomplete') ? 'Incomplete' : 'Completed';
                $this->document->update([
                    'status' => 'Approved',
                    'document_completeness' => $completeness,
                    'approved_at' => now(),
                    'last_modified_by' => $user->nip . ' - ' . $user->name,
                ]);
                $this->document->logWorkflow('Approved', $this->actionNotes ?: "Disetujui Biro Payment ({$completeness})");
                session()->flash('success', "Dokumen berhasil disetujui (Kelengkapan Dokumen: {$completeness})!");
            }
        }

        $this->showActionModal = false;
        $this->document->refresh();
    }

    public function markAsPaid(): void
    {
        $this->document->update([
            'payment_status' => 'Paid',
            'paid_at' => now(),
            'last_modified_by' => auth()->user()->nip . ' - ' . auth()->user()->name,
        ]);
        $this->document->logWorkflow('Paid', 'Pembayaran ditransfer melalui sistem BCA');
        session()->flash('success', 'Pembayaran berhasil ditandai Paid!');
        $this->document->refresh();
    }

    public function render()
    {
        return view('livewire.gista.payment-display');
    }
}
