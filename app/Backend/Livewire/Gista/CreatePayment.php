<?php

namespace App\Backend\Livewire\Gista;

use App\Backend\Models\AdditionalCost;
use App\Backend\Models\CostAllocation;
use App\Backend\Models\DocumentAttachment;
use App\Backend\Models\EsgRecord;
use App\Backend\Models\PaymentDocument;
use App\Backend\Models\PaymentItem;
use App\Backend\Models\TaxInvoice;
use App\Backend\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('frontend.layouts.gista')]
class CreatePayment extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    // Step 1: Referensi Pembayaran
    public string $poNumber = 'POST25001876';
    public bool $poChecked = false;
    public string $poFeedback = '';
    public $dcFile; // file .txt/.csv untuk memilih item DC
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
    public string $submitError = ''; // popup instead of 429/403 page

    // Quick summary modals on Step 7
    public bool $showQuickFakturModal = false;
    public bool $showQuickEsgModal = false;
    public bool $showQuickJurnalModal = false;

    public ?int $editingDocumentId = null;
    public bool $isEditMode = false;

    public function mount(): void
    {
        $this->dueDate = Carbon::now()->addDay()->toDateString();
        $this->taxInvoiceDate = Carbon::now()->toDateString();
        $this->vendorInvoiceDate = Carbon::now()->toDateString();

        $document = null;
        if (request()->has('document')) {
            $document = PaymentDocument::find(request('document'));
        }
        if ($document instanceof PaymentDocument) {
            if (! $document->canBeEditedBy(auth()->user())) {
                session()->flash('error', 'Akses Ditolak: dokumen ini tidak dapat diedit.');
                $this->redirect(route('dashboard'), navigate: true);
                return;
            }
            $this->editingDocumentId = $document->id;
            $this->isEditMode = true;
            $this->hydrateForEdit($document);
            return;
        }

        $this->checkPo();
    }

    private function hydrateForEdit(PaymentDocument $document): void
    {
        $document->load(['items', 'taxInvoices', 'additionalCosts', 'allocations', 'esgRecords', 'attachments']);

        $this->poNumber = $document->po_number;
        $this->poChecked = true;
        $this->poFeedback = 'Data pengajuan dimuat untuk diperbaiki.';
        $this->vendorCode = $document->vendor_code;
        $this->vendorName = $document->vendor_name;
        $this->docDescription = $document->description ?? $this->docDescription;
        $this->currency = $document->currency ?: 'IDR';
        $this->dueDate = $document->due_date?->toDateString() ?: $this->dueDate;
        $this->hasDueDateRequest = (bool) $document->has_due_date_request;
        $this->requestedDueDate = $document->requested_due_date?->toDateString() ?: '';
        $this->delayReason = $document->delay_reason ?: $this->delayReason;
        $this->delayNotes = $document->delay_notes ?: '';
        $this->vendorInvoiceNo = $document->vendor_invoice_number ?: $this->vendorInvoiceNo;
        $this->vendorInvoiceDate = $document->vendor_invoice_date?->toDateString() ?: $this->vendorInvoiceDate;
        $this->dppAccount = $document->dpp_account_number ?: $this->dppAccount;
        $this->dppAccountName = $document->dpp_account_name ?: $this->dppAccountName;
        $this->dppBank = $document->dpp_bank_name ?: $this->dppBank;
        $this->ppnAccount = $document->ppn_account_number ?: $this->ppnAccount;
        $this->ppnAccountName = $document->ppn_account_name ?: $this->ppnAccountName;
        $this->ppnBank = $document->ppn_bank_name ?: $this->ppnBank;
        $this->paymentNotes = $document->payment_notes ?: $this->paymentNotes;

        $this->dcItems = $document->items->map(fn ($item) => [
            'line_item_po' => $item->line_item_po,
            'item_code' => $item->item_code,
            'item_name' => $item->item_name,
            'dc_number' => $item->dc_number,
            'ppn_code' => $item->ppn_code,
            'pph_code' => $item->pph_code,
            'nominal' => (float) $item->nominal,
            'status_dc' => $item->status_dc,
            'transaction_type' => $item->transaction_type,
            'slid' => $item->slid,
            'coa' => $item->coa,
            'coa_name' => $item->coa_name,
            'sub_coa' => $item->sub_coa,
            'product_code' => $item->product_code,
            'cost_center' => $item->cost_center,
            'cost_center_name' => $item->cost_center_name,
            'budget_code' => $item->budget_code,
        ])->all();
        $this->selectedDc = $document->items->pluck('dc_number')->all();

        $this->taxInvoices = $document->taxInvoices->map(fn ($tax) => [
            'type' => $tax->type,
            'invoice_number' => $tax->invoice_number,
            'invoice_date' => $tax->invoice_date?->toDateString() ?: now()->toDateString(),
            'dpp' => (float) $tax->dpp_idr,
            'ppn' => (float) $tax->ppn_idr,
        ])->all();

        $this->additionalCosts = $document->additionalCosts->map(fn ($cost) => [
            'type' => $cost->type,
            'coa' => $cost->coa,
            'sub_coa' => $cost->sub_coa,
            'slid' => $cost->slid,
            'product_code' => $cost->product_code,
            'cost_center' => $cost->cost_center,
            'amount' => (float) $cost->amount,
        ])->all();

        $this->allocations = $document->allocations->map(fn ($alloc) => [
            'item' => $alloc->item_description ?: '',
            'amount' => (float) $alloc->amount,
            'cost_center' => $alloc->cost_center,
            'notes' => $alloc->notes,
        ])->all();

        $this->esgRecords = $document->esgRecords->map(fn ($esg) => [
            'esg_type' => $esg->esg_type,
            'cost_center' => $esg->cost_center,
            'unit' => $esg->unit,
            'usage_amount' => (float) $esg->usage_amount,
            'unit_price' => (float) $esg->unit_price,
            'total_amount' => (float) $esg->total_amount,
            'billing_period' => $esg->billing_period,
            'notes' => $esg->notes,
        ])->all();

        $this->attachments = $document->attachments->map(fn ($att) => [
            'id' => $att->id,
            'type' => $att->doc_type,
            'name' => $att->file_name,
            'path' => $att->file_path,
            'notes' => $att->notes,
        ])->all();

        $this->selectedSpv1Id = $document->spv1_id;
        $this->withChecker = (bool) $document->checker_id;
        $this->selectedCheckerId = $document->checker_id;

        $this->currentStep = 1;
    }

    public function checkPo(): void
    {
        $this->poFeedback = '';

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

            $this->poFeedback = 'PO ditemukan. Data vendor, item DC, faktur pajak, alokasi, dan ESG berhasil dimuat.';

            return;
        }

        $this->poChecked = false;
        $this->dcItems = [];
        $this->selectedDc = [];
        $this->poFeedback = 'PO tidak ditemukan. Untuk demo gunakan nomor POST25001876.';
    }

    /**
     * Pilih item DC dari file .txt/.csv (satu kode DC per baris atau dipisah koma).
     */
    public function updatedDcFile(): void
    {
        $this->validate([
            'dcFile' => 'required|file|max:1024|mimes:txt,csv',
        ], [
            'dcFile.mimes' => 'File harus berformat .txt atau .csv.',
            'dcFile.max' => 'Ukuran file maksimal 1 MB.',
        ]);

        $content = file_get_contents($this->dcFile->getRealPath());
        $codes = preg_split('/[\s,;]+/', (string) $content, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $available = array_column($this->dcItems, 'dc_number');
        $matched = array_values(array_intersect($available, array_map('trim', $codes)));

        if (empty($matched)) {
            $this->reset('dcFile');
            session()->flash('error', 'Tidak ada nomor DC yang cocok dengan PO ini. Periksa kembali isi file.');
            return;
        }

        $this->selectedDc = array_values(array_unique($matched));
        $this->reset('dcFile');
        $this->poFeedback = count($matched) . ' item DC dipilih dari file.';
    }

    public function openFakturUpload(): void
    {
        $this->attachType = 'Faktur Pajak';
        $this->showAttachModal = true;
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
        $this->validate([
            'esgType' => 'required|string|max:100',
            'esgCostCenter' => 'required|string|max:50',
            'esgUnit' => 'required|string|max:20',
            'esgUsage' => 'required|numeric|min:0.01',
            'esgUnitPrice' => 'required|numeric|min:0',
            'esgTotal' => 'required|numeric|min:0',
            'esgPeriod' => 'required|string|max:20',
            'esgNotes' => 'nullable|string|max:500',
        ]);

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
            'attachFile' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $allowedMimes = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/jpeg',
                        'image/png',
                    ];
                    $mime = $value->getMimeType();
                    if (! in_array($mime, $allowedMimes, true)) {
                        $fail('Format file tidak diizinkan. Gunakan PDF, DOC, DOCX, XLS, XLSX, JPG, atau PNG.');
                    }
                },
            ],
        ]);

        $mimeToExtension = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        $original = $this->attachFile->getClientOriginalName();
        $safeName = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $original);
        // Ekstensi diturunkan dari MIME terverifikasi, bukan dari input client.
        $extension = $mimeToExtension[$this->attachFile->getMimeType()] ?? 'bin';
        $safeName = pathinfo($safeName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;

        $path = $this->attachFile->storeAs('attachments', $safeName, 'local');

        $this->attachments[] = [
            'type' => $this->attachType,
            'name' => $original,
            'path' => $path,
            'notes' => $this->attachNotes,
        ];

        $this->reset(['attachFile', 'attachNotes', 'showAttachModal']);
    }

    // Submit Document (PDF Page 30-31)
    public function submitPayment()
    {
        $key = 'submit_payment:' . auth()->id();
        if (!RateLimiter::attempt($key, 5, function () {
            return true;
        }, 60)) {
            $this->submitError = 'Terlalu banyak pengajuan. Coba lagi dalam 1 menit.';
            return;
        }

        $this->validate([
            'selectedSpv1Id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->whereIn('role', ['spv1', 'supervisor_eksternal']),
            ],
            'selectedCheckerId' => [
                Rule::requiredIf($this->withChecker),
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'checker'),
            ],
        ], [
            'selectedSpv1Id.exists' => 'Supervisor 1 yang dipilih tidak valid.',
            'selectedCheckerId.required' => 'Checker wajib dipilih jika verifikasi checker diaktifkan.',
            'selectedCheckerId.exists' => 'Checker yang dipilih tidak valid.',
        ]);

        try {
            $document = DB::transaction(function () {
                $targetStatus = $this->withChecker ? 'Waiting for Checker' : 'Waiting for Approval 1';

                $data = [
                    'po_number' => $this->poNumber,
                    'po_termin_type' => 'PO Non Termin',
                    'vendor_code' => $this->vendorCode,
                    'vendor_name' => $this->vendorName,
                    'description' => $this->docDescription,
                    'currency' => $this->currency,
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
                    'spv1_id' => $this->selectedSpv1Id,
                    'checker_id' => $this->withChecker ? $this->selectedCheckerId : null,
                    'last_modified_by' => auth()->user()->nip . ' - ' . auth()->user()->name,
                    'submitted_at' => now(),
                    'sla_due_at' => now()->addDays(3),
                ];

                if ($this->isEditMode && $this->editingDocumentId) {
                    $document = PaymentDocument::findOrFail($this->editingDocumentId)->fresh();
                    $document->update(array_merge($data, [
                        'status' => $document->returned_from_status ?: $targetStatus,
                        'returned_from_status' => null,
                        'returned_by' => null,
                        'returned_at' => null,
                        'pending_reason' => null,
                        'pending_by' => null,
                        'pending_at' => null,
                        'pending_from_status' => null,
                    ]));
                    $document->items()->delete();
                    $document->taxInvoices()->delete();
                    $document->additionalCosts()->delete();
                    $document->allocations()->delete();
                    $document->esgRecords()->delete();
                } else {
                    $data['optg_number'] = PaymentDocument::generateOptgNumber();
                    $data['po_type'] = 'PO Standar';
                    $data['po_termin_type'] = 'PO Non Termin';
                    $data['status'] = $targetStatus;
                    $data['created_by'] = auth()->id();
                    $document = PaymentDocument::create($data);
                }

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

                // Save Attachments (skip yang sudah tersimpan di DB saat edit mode)
                foreach ($this->attachments as $att) {
                    if (! empty($att['id'])) {
                        continue;
                    }
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
                if ($this->isEditMode) {
                    $document->logWorkflow('Resubmitted', $this->submitNotes ?: 'Pengajuan tagihan pembayaran diperbaiki dan diajukan ulang');
                } else {
                    $document->logWorkflow('Submitted', $this->submitNotes ?: 'Pengajuan tagihan pembayaran');
                    if ($this->withChecker) {
                        $document->logWorkflow('Waiting for Checker', 'Menunggu verifikasi checker');
                    } else {
                        $document->logWorkflow('Waiting for Approval 1', 'Menunggu verifikasi Supervisor Unit Kerja');
                    }
                }
                if ($targetStatus === 'Waiting for Checker' && $this->isEditMode) {
                    $document->logWorkflow('Waiting for Checker', 'Menunggu verifikasi checker');
                } elseif ($targetStatus === 'Waiting for Approval 1' && $this->isEditMode) {
                    $document->logWorkflow('Waiting for Approval 1', 'Menunggu verifikasi Supervisor Unit Kerja');
                }

                return $document;
            });

            if ($this->isEditMode) {
                session()->flash('success', "Dokumen {$document->optg_number} berhasil diperbaiki dan diajukan ulang!");
            } else {
                session()->flash('success', "Dokumen {$document->optg_number} berhasil dibuat dan diajukan!");
            }
            return $this->redirect(route('payment.show', $document->id), navigate: true);
        } catch (\Exception $e) {
            $this->submitError = 'Gagal menyimpan dokumen. Silakan coba lagi.';
            report($e);
        }
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

        return view('frontend.gista.create-payment', [
            'supervisors' => $supervisors,
            'checkers' => $checkers,
        ]);
    }
}
