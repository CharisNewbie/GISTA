<?php

namespace App\Livewire\Gista;

use App\Models\AdditionalCost;
use App\Models\CostAllocation;
use App\Models\DocumentAttachment;
use App\Models\EsgRecord;
use App\Models\PaymentDocument;
use App\Models\PaymentItem;
use App\Models\TaxInvoice;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.gista')]
class CreatePayment extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    // Step 1: Referensi Pembayaran
    public string $poNumber = 'POST25001876';
    public bool $poChecked = false;
    public string $vendorCode = '1000018186';
    public string $vendorName = 'PT. GISTA MAJU BERSAMA';
    public string $docDescription = 'Pengadaan Barang Logistik Operasional Cabang Bandung';
    public string $currency = 'IDR';
    public array $dcItems = [];
    public array $selectedDc = [];

    // Step 2: Faktur Pajak
    public array $taxInvoices = [];
    public bool $showTaxModal = false;
    public string $taxType = 'Standar';
    public string $taxInvoiceNumber = '';
    public string $taxInvoiceDate = '';
    public float $taxDpp = 0;
    public float $taxPpn = 0;

    // Step 3: Detail Pembebanan
    public string $pembebananTab = 'items'; // items or additional
    public array $additionalCosts = [];
    public bool $showAdditionalModal = false;
    public string $addCostType = 'Biaya Tambahan (Db)';
    public string $addCostCoa = '691144';
    public string $addCostSubCoa = '00';
    public string $addCostProductCode = '000';
    public string $addCostCostCenter = 'C00998031';
    public float $addCostAmount = 0;

    // Step 4: Detail Pembayaran
    public string $dueDate = '';
    public bool $hasDueDateRequest = false;
    public string $requestedDueDate = '';
    public string $delayReason = 'Urgensi Tagihan Vendor';
    public string $delayNotes = '';
    public string $vendorInvoiceNo = 'LMA08.2025-1023/01';
    public string $vendorInvoiceDate = '';
    public string $dppAccount = '983475437634967';
    public string $dppAccountName = 'Abi - PT Gista Maju Bersama';
    public string $dppBank = 'PT. BANK CENTRAL ASIA Tbk.';
    public string $ppnAccount = '983475437634967';
    public string $ppnAccountName = 'Abi - PT Gista Maju Bersama';
    public string $ppnBank = 'PT. BANK CENTRAL ASIA Tbk.';
    public string $paymentNotes = 'Pembayaran tagihan barang logistik termin 1';

    // Step 5: Alokasi
    public array $allocations = [];
    public bool $showAllocationModal = false;
    public string $allocItem = '';
    public float $allocAmount = 0;
    public string $allocCostCenter = 'C00971033';
    public string $allocNotes = 'Alokasi ke unit kanwil 1';

    // Step 6: ESG
    public array $esgRecords = [];
    public bool $showEsgModal = false;
    public string $esgType = 'Listrik';
    public string $esgCostCenter = 'C00998031';
    public string $esgUnit = 'kwh';
    public float $esgUsage = 2500;
    public float $esgUnitPrice = 1760;
    public float $esgTotal = 4400000;
    public string $esgPeriod = '07/2024';
    public string $esgNotes = 'Pembayaran listrik gedung kanwil';

    // Step 7: Summary & Attachments & Submit
    public array $attachments = [];
    public bool $showAttachModal = false;
    public string $attachType = 'Invoice Vendor';
    public $attachFile;
    public string $attachNotes = '';

    // Submit Modal
    public bool $showSubmitModal = false;
    public ?int $selectedSpv1Id = null;
    public bool $withChecker = false;
    public ?int $selectedCheckerId = null;
    public string $submitNotes = '';

    // Quick summary modals on Step 7
    public bool $showQuickFakturModal = false;
    public bool $showQuickEsgModal = false;
    public bool $showQuickJurnalModal = false;

    public function mount(): void
    {
        $this->dueDate = Carbon::now()->addDay()->toDateString();
        $this->taxInvoiceDate = Carbon::now()->toDateString();
        $this->vendorInvoiceDate = Carbon::now()->toDateString();
        $this->checkPo();
    }

    public function checkPo(): void
    {
        if ($this->poNumber === 'POST25001876') {
            $this->poChecked = true;
            $this->vendorCode = '1000018186';
            $this->vendorName = 'PT. GISTA MAJU BERSAMA';
            $this->currency = 'IDR';

            $this->dcItems = [
                [
                    'line_item_po' => '1.0-1',
                    'item_code' => 'B0000637',
                    'item_name' => 'Barang Biaya',
                    'dc_number' => 'DC25001876-0001',
                    'ppn_code' => 'M1',
                    'pph_code' => '21C - Non NPWP',
                    'nominal' => 1500000.00,
                    'status_dc' => 'Fully Received',
                    'transaction_type' => 'Pembebanan Langsung',
                    'slid' => 'PPOST2500001',
                    'coa' => '691084',
                    'coa_name' => 'Biaya Angkutan',
                    'sub_coa' => '00',
                    'product_code' => '000',
                    'cost_center' => 'C00008600',
                    'cost_center_name' => 'SUPPORT KCU BANDUNG',
                    'budget_code' => 'C00008600',
                ],
                [
                    'line_item_po' => '1.0-2',
                    'item_code' => 'B0000637',
                    'item_name' => 'Barang Biaya',
                    'dc_number' => 'DC25001876-0002',
                    'ppn_code' => 'M1',
                    'pph_code' => '21C - Non NPWP',
                    'nominal' => 2000000.00,
                    'status_dc' => 'Fully Received',
                    'transaction_type' => 'Pembebanan Langsung',
                    'slid' => 'PPOST2500001',
                    'coa' => '691084',
                    'coa_name' => 'Biaya Angkutan',
                    'sub_coa' => '00',
                    'product_code' => '000',
                    'cost_center' => 'C00008600',
                    'cost_center_name' => 'SUPPORT KCU BANDUNG',
                    'budget_code' => 'C00008600',
                ],
                [
                    'line_item_po' => '1.0-3',
                    'item_code' => 'B0000637',
                    'item_name' => 'Barang Biaya',
                    'dc_number' => 'DC25001876-0003',
                    'ppn_code' => 'M1',
                    'pph_code' => '21C - Non NPWP',
                    'nominal' => 2000000.00,
                    'status_dc' => 'Fully Received',
                    'transaction_type' => 'Pembebanan Langsung',
                    'slid' => 'PPOST2500001',
                    'coa' => '691084',
                    'coa_name' => 'Biaya Angkutan',
                    'sub_coa' => '00',
                    'product_code' => '000',
                    'cost_center' => 'C00008600',
                    'cost_center_name' => 'SUPPORT KCU BANDUNG',
                    'budget_code' => 'C00008600',
                ],
            ];
            $this->selectedDc = ['DC25001876-0001', 'DC25001876-0002', 'DC25001876-0003'];

            // Initialize default matching tax invoice
            $this->taxInvoices = [
                [
                    'type' => 'Standar',
                    'invoice_number' => '1234-5678-9101-11213',
                    'invoice_date' => Carbon::now()->toDateString(),
                    'dpp' => 5500000.00,
                    'ppn' => 605000.00,
                ]
            ];

            // Initialize default allocations
            $this->allocations = [
                [
                    'item' => 'DC25001876-0001 (Barang Biaya)',
                    'amount' => 5500000.00,
                    'cost_center' => 'C00971033 - KANWIL I',
                    'notes' => 'Pembebanan KCU Bandung',
                ]
            ];

            // Initialize default ESG
            $this->esgRecords = [
                [
                    'esg_type' => 'Listrik',
                    'cost_center' => 'C00998031 - Gedung Logistik',
                    'unit' => 'kwh',
                    'usage_amount' => 2500,
                    'unit_price' => 1760,
                    'total_amount' => 4400000,
                    'billing_period' => '07/2024',
                    'notes' => 'Pembayaran listrik gedung kanwil',
                ]
            ];
        }
    }

    public function getTotalDppProperty(): float
    {
        $sum = 0;
        foreach ($this->dcItems as $item) {
            if (in_array($item['dc_number'], $this->selectedDc)) {
                $sum += (float) $item['nominal'];
            }
        }
        return $sum;
    }

    public function getTotalTaxDppProperty(): float
    {
        return array_sum(array_column($this->taxInvoices, 'dpp'));
    }

    public function getTotalTaxPpnProperty(): float
    {
        return array_sum(array_column($this->taxInvoices, 'ppn'));
    }

    public function getSelisihDppProperty(): float
    {
        return $this->totalDpp - $this->totalTaxDpp;
    }

    public function getTotalAdditionalCostProperty(): float
    {
        return array_sum(array_column($this->additionalCosts, 'amount'));
    }

    public function getTotalPaymentProperty(): float
    {
        return $this->totalDpp + $this->totalTaxPpn + $this->totalAdditionalCost;
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            if (empty($this->selectedDc)) {
                session()->flash('error', 'Pilih minimal 1 item DC yang akan dibayar.');
                return;
            }
        }

        if ($this->currentStep === 2) {
            if (abs($this->selisihDpp) > 0.01) {
                session()->flash('error', 'Selisih DPP harus 0 (balance) untuk dapat melanjutkan.');
                return;
            }
        }

        if ($this->currentStep < 7) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    // Tax Invoice Modal Actions
    public function addTaxInvoice(): void
    {
        $this->validate([
            'taxInvoiceNumber' => 'required|min:5',
            'taxDpp' => 'required|numeric|min:1',
            'taxPpn' => 'required|numeric|min:0',
        ]);

        $this->taxInvoices[] = [
            'type' => $this->taxType,
            'invoice_number' => $this->taxInvoiceNumber,
            'invoice_date' => $this->taxInvoiceDate ?: now()->toDateString(),
            'dpp' => (float) $this->taxDpp,
            'ppn' => (float) $this->taxPpn,
        ];

        $this->reset(['taxInvoiceNumber', 'taxDpp', 'taxPpn', 'showTaxModal']);
    }

    public function removeTaxInvoice(int $index): void
    {
        unset($this->taxInvoices[$index]);
        $this->taxInvoices = array_values($this->taxInvoices);
    }

    // Additional Cost Actions
    public function addAdditionalCost(): void
    {
        $this->validate([
            'addCostAmount' => 'required|numeric|min:1',
        ]);

        $this->additionalCosts[] = [
            'type' => $this->addCostType,
            'coa' => $this->addCostCoa,
            'sub_coa' => $this->addCostSubCoa,
            'slid' => 'TPORG24000503',
            'product_code' => $this->addCostProductCode,
            'cost_center' => $this->addCostCostCenter,
            'amount' => (float) $this->addCostAmount,
        ];

        $this->reset(['addCostAmount', 'showAdditionalModal']);
    }

    public function removeAdditionalCost(int $index): void
    {
        unset($this->additionalCosts[$index]);
        $this->additionalCosts = array_values($this->additionalCosts);
    }

    // Allocation Actions
    public function addAllocation(): void
    {
        $this->validate([
            'allocAmount' => 'required|numeric|min:1',
        ]);

        $this->allocations[] = [
            'item' => $this->allocItem ?: 'DC25001876-0001',
            'amount' => (float) $this->allocAmount,
            'cost_center' => $this->allocCostCenter,
            'notes' => $this->allocNotes,
        ];

        $this->reset(['allocAmount', 'showAllocationModal']);
    }

    public function removeAllocation(int $index): void
    {
        unset($this->allocations[$index]);
        $this->allocations = array_values($this->allocations);
    }

    // ESG Actions
    public function addEsg(): void
    {
        $this->esgRecords[] = [
            'esg_type' => $this->esgType,
            'cost_center' => $this->esgCostCenter,
            'unit' => $this->esgUnit,
            'usage_amount' => (float) $this->esgUsage,
            'unit_price' => (float) $this->esgUnitPrice,
            'total_amount' => (float) $this->esgTotal,
            'billing_period' => $this->esgPeriod,
            'notes' => $this->esgNotes,
        ];

        $this->reset(['showEsgModal']);
    }

    public function removeEsg(int $index): void
    {
        unset($this->esgRecords[$index]);
        $this->esgRecords = array_values($this->esgRecords);
    }

    // Attachment Actions
    public function addAttachment(): void
    {
        $this->validate([
            'attachFile' => 'required|file|max:10240',
        ]);

        $path = $this->attachFile->store('attachments', 'public');

        $this->attachments[] = [
            'type' => $this->attachType,
            'name' => $this->attachFile->getClientOriginalName(),
            'path' => $path,
            'notes' => $this->attachNotes,
        ];

        $this->reset(['attachFile', 'attachNotes', 'showAttachModal']);
    }

    // Submit Document (PDF Page 30-31)
    public function submitPayment()
    {
        $this->validate([
            'selectedSpv1Id' => 'required|exists:users,id',
        ]);

        $optgNumber = PaymentDocument::generateOptgNumber();

        $document = PaymentDocument::create([
            'optg_number' => $optgNumber,
            'po_type' => 'PO Standar',
            'po_number' => $this->poNumber,
            'po_termin_type' => 'PO Non Termin',
            'vendor_code' => $this->vendorCode,
            'vendor_name' => $this->vendorName,
            'description' => $this->docDescription,
            'currency' => $this->currency,
            'status' => $this->withChecker ? 'Waiting for Checker' : 'Waiting for Approval 1',
            'payment_status' => 'Payment In Process',
            'document_completeness' => 'Pending',
            'due_date' => $this->dueDate,
            'has_due_date_request' => $this->hasDueDateRequest,
            'requested_due_date' => $this->hasDueDateRequest ? $this->requestedDueDate : null,
            'delay_reason' => $this->hasDueDateRequest ? $this->delayReason : null,
            'delay_notes' => $this->hasDueDateRequest ? $this->delayNotes : null,
            'vendor_invoice_number' => $this->vendorInvoiceNo,
            'vendor_invoice_date' => $this->vendorInvoiceDate,
            'dpp_account_number' => $this->dppAccount,
            'dpp_account_name' => $this->dppAccountName,
            'dpp_bank_name' => $this->dppBank,
            'ppn_account_number' => $this->ppnAccount,
            'ppn_account_name' => $this->ppnAccountName,
            'ppn_bank_name' => $this->ppnBank,
            'payment_notes' => $this->paymentNotes,
            'total_dpp' => $this->totalDpp,
            'total_ppn' => $this->totalTaxPpn,
            'total_additional_cost' => $this->totalAdditionalCost,
            'total_payment' => $this->totalPayment,
            'created_by' => auth()->id(),
            'spv1_id' => $this->selectedSpv1Id,
            'checker_id' => $this->withChecker ? $this->selectedCheckerId : null,
            'last_modified_by' => auth()->user()->nip . ' - ' . auth()->user()->name,
            'submitted_at' => now(),
        ]);

        // Save Items
        foreach ($this->dcItems as $item) {
            if (in_array($item['dc_number'], $this->selectedDc)) {
                PaymentItem::create([
                    'payment_document_id' => $document->id,
                    'line_item_po' => $item['line_item_po'],
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'],
                    'dc_number' => $item['dc_number'],
                    'ppn_code' => $item['ppn_code'],
                    'pph_code' => $item['pph_code'],
                    'nominal' => $item['nominal'],
                    'status_dc' => $item['status_dc'],
                    'transaction_type' => $item['transaction_type'],
                    'slid' => $item['slid'],
                    'coa' => $item['coa'],
                    'coa_name' => $item['coa_name'],
                    'sub_coa' => $item['sub_coa'],
                    'product_code' => $item['product_code'],
                    'cost_center' => $item['cost_center'],
                    'cost_center_name' => $item['cost_center_name'],
                    'budget_code' => $item['budget_code'],
                    'is_selected' => true,
                ]);
            }
        }

        // Save Tax Invoices
        foreach ($this->taxInvoices as $tax) {
            TaxInvoice::create([
                'payment_document_id' => $document->id,
                'type' => $tax['type'],
                'invoice_number' => $tax['invoice_number'],
                'invoice_date' => $tax['invoice_date'],
                'currency' => 'IDR',
                'dpp_idr' => $tax['dpp'],
                'ppn_idr' => $tax['ppn'],
            ]);
        }

        // Save Additional Costs
        foreach ($this->additionalCosts as $cost) {
            AdditionalCost::create([
                'payment_document_id' => $document->id,
                'type' => $cost['type'],
                'coa' => $cost['coa'],
                'sub_coa' => $cost['sub_coa'],
                'slid' => $cost['slid'],
                'product_code' => $cost['product_code'],
                'cost_center' => $cost['cost_center'],
                'amount' => $cost['amount'],
            ]);
        }

        // Save Allocations
        foreach ($this->allocations as $alloc) {
            CostAllocation::create([
                'payment_document_id' => $document->id,
                'item_description' => $alloc['item'] ?? null,
                'amount' => $alloc['amount'],
                'cost_center' => $alloc['cost_center'],
                'notes' => $alloc['notes'] ?? null,
            ]);
        }

        // Save ESG Records
        foreach ($this->esgRecords as $esg) {
            EsgRecord::create([
                'payment_document_id' => $document->id,
                'esg_type' => $esg['esg_type'],
                'cost_center' => $esg['cost_center'],
                'unit' => $esg['unit'],
                'usage_amount' => $esg['usage_amount'],
                'unit_price' => $esg['unit_price'],
                'total_amount' => $esg['total_amount'],
                'billing_period' => $esg['billing_period'],
                'notes' => $esg['notes'] ?? null,
            ]);
        }

        // Save Attachments
        foreach ($this->attachments as $att) {
            DocumentAttachment::create([
                'payment_document_id' => $document->id,
                'doc_type' => $att['type'],
                'file_name' => $att['name'],
                'file_path' => $att['path'],
                'notes' => $att['notes'],
                'uploaded_by' => auth()->user()->nip . ' - ' . auth()->user()->name,
            ]);
        }

        // Log Workflow (PDF Page 39)
        $document->logWorkflow('Submitted', $this->submitNotes ?: 'Pengajuan tagihan pembayaran');
        if ($this->withChecker) {
            $document->logWorkflow('Waiting for Checker', 'Menunggu verifikasi checker');
        } else {
            $document->logWorkflow('Waiting for Approval 1', 'Menunggu verifikasi Supervisor Unit Kerja');
        }

        session()->flash('success', "Dokumen {$optgNumber} berhasil dibuat dan diajukan!");
        return $this->redirect(route('payment.show', $document->id), navigate: true);
    }

    public function render()
    {
        $supervisors = User::whereIn('role', ['spv1', 'supervisor_eksternal'])->get();
        $checkers = User::where('role', 'checker')->get();

        if (!$this->selectedSpv1Id && $supervisors->isNotEmpty()) {
            $this->selectedSpv1Id = $supervisors->first()->id;
        }
        if (!$this->selectedCheckerId && $checkers->isNotEmpty()) {
            $this->selectedCheckerId = $checkers->first()->id;
        }

        return view('livewire.gista.create-payment', [
            'supervisors' => $supervisors,
            'checkers' => $checkers,
        ]);
    }
}
