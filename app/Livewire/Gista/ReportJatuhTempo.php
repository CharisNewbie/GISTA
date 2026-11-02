<?php

namespace App\Livewire\Gista;

use App\Models\PaymentDocument;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.gista')]
class ReportJatuhTempo extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'All';
    public ?PaymentDocument $selectedDoc = null;
    public bool $showStatusModal = false;

    public function viewStatus(int $id): void
    {
        $this->selectedDoc = PaymentDocument::findOrFail($id);
        $this->showStatusModal = true;
    }

    public function exportCsv()
    {
        // ponytail: CSV streaming via stdlib fopen/fputcsv; upgrade to Maatwebsite Excel when complex formatting/charts needed.
        $docs = PaymentDocument::query()->latest()->get();
        $filename = 'report-jatuh-tempo-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($docs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No. Pembayaran Tagihan', 'No. PO', 'Vendor', 'Mata Uang', 'Nilai Tagihan', 'Jatuh Tempo', 'Status Dokumen', 'Status Bayar']);
            foreach ($docs as $d) {
                fputcsv($handle, [
                    $d->optg_number,
                    $d->po_number,
                    $d->vendor_name,
                    $d->currency,
                    $d->total_payment,
                    $d->due_date?->format('d/m/Y'),
                    $d->status,
                    $d->payment_status,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $query = PaymentDocument::query()->latest('due_date');

        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('optg_number', 'like', "%{$this->search}%")
                  ->orWhere('vendor_name', 'like', "%{$this->search}%")
                  ->orWhere('po_number', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.gista.report-jatuh-tempo', [
            'documents' => $query->paginate(15),
        ]);
    }
}
