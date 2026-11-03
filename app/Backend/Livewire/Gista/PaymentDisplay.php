<?php

namespace App\Backend\Livewire\Gista;

use App\Backend\Models\PaymentDocument;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('frontend.layouts.gista')]
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
    public string $authError = ''; // popup error message instead of 403 page

    public function mount(PaymentDocument $document): void
    {
        $this->document = $document->load(['items', 'taxInvoices', 'additionalCosts', 'attachments', 'workflowLogs', 'allocations', 'esgRecords']);

        // Cegah IDOR: user hanya boleh membuka dokumen yang menjadi haknya.
        if (! $this->document->canBeViewedBy(auth()->user())) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki akses ke dokumen ini.');
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function openAction(string $type): void
    {
        $user = auth()->user();

        if (! in_array($type, ['approve', 'reject', 'pending'], true)) {
            $this->authError = 'Tindakan tidak dikenal.';
            return;
        }

        // Role + penugasan: hanya approver yang ditugaskan pada dokumen ini.
        if (! $this->document->canBeActedOnBy($user)) {
            $this->authError = 'Anda tidak ditugaskan atau tidak berwenang melakukan tindakan ini pada dokumen ini.';
            return;
        }

        $this->actionType = $type;
        $this->actionNotes = '';
        $this->showActionModal = true;
    }

    public function confirmAction(): void
    {
        // Re-validate role + penugasan server-side (defense in depth, popup bukan 403)
        $user = auth()->user();
        if (! $this->document->canBeActedOnBy($user)) {
            $this->showActionModal = false;
            $this->authError = 'Anda tidak ditugaskan atau tidak berwenang melakukan tindakan ini pada dokumen ini.';
            return;
        }

        $key = 'payment_action:' . auth()->id();
        if (!RateLimiter::attempt($key, 10, function () {
            return true;
        }, 60)) {
            $this->authError = 'Terlalu banyak tindakan. Coba lagi dalam 1 menit.';
            return;
        }

        if ($this->actionType === 'reject') {
            if (trim($this->actionNotes) === '') {
                $this->addError('actionNotes', 'Alasan penolakan wajib diisi.');
                return;
            }
            $this->document->update([
                'status' => 'Correction',
                'returned_from_status' => $this->document->status === 'Pending' ? 'Waiting for Approval 2' : $this->document->status,
                'returned_by' => $user->id,
                'returned_at' => now(),
                'last_modified_by' => $user->nip . ' - ' . $user->name,
            ]);
            $this->document->logWorkflow('Correction', $this->actionNotes);
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
            } elseif ($this->document->status === 'Waiting for Approval 2' || $this->document->status === 'Pending') {
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
        $key = 'mark_as_paid:' . auth()->id();
        if (!RateLimiter::attempt($key, 5, function () {
            return true;
        }, 60)) {
            $this->authError = 'Terlalu banyak permintaan. Coba lagi dalam 1 menit.';
            return;
        }

        // Biro Payment (SPV 2) boleh menandai pembayaran semua dokumen yang Approved
        if (!auth()->user()->isSpv2()) {
            $this->authError = 'Hanya SPV 2 yang dapat menandai pembayaran sebagai Paid.';
            return;
        }
        if ($this->document->status !== 'Approved' || $this->document->payment_status === 'Paid') {
            $this->authError = 'Dokumen belum disetujui atau sudah dibayarkan.';
            return;
        }

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
        return view('frontend.gista.payment-display');
    }
}
