<div class="max-w-7xl mx-auto space-y-6 pb-16">
    <!-- Authorization error popup (no 403 web page) -->
    @if($authError)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed top-5 right-5 z-[100] max-w-sm bg-white border border-rose-200 rounded-2xl shadow-xl p-4 flex items-start space-x-3">
            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold text-rose-800">Akses Ditolak</p>
                <p class="text-xs text-slate-600 mt-0.5" x-text="$wire.authError"></p>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Page Title -->
    <div class="flex items-end justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Display Pembayaran</h2>
            <p class="text-xs text-slate-500 mt-0.5">Review dokumen, kelengkapan, dan riwayat persetujuan pembayaran</p>
        </div>
    </div>

    <!-- Top Action Bar (PDF Page 32, 33, 36, 40) -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('payment.index') }}" class="inline-flex items-center px-3.5 py-1.5 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>

            <!-- Quick Modals -->
            <button wire:click="$set('showFakturModal', true)" class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gista-50 border border-gista-400 text-gista-600 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                Faktur Pajak
            </button>
            <button wire:click="$set('showEsgModal', true)" class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gista-50 border border-gista-400 text-gista-600 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                ESG
            </button>
            <button wire:click="$set('showJurnalModal', true)" class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gista-50 border border-gista-400 text-gista-600 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                Jurnal
            </button>

            <!-- Workflow Log Trigger (PDF Page 39) -->
            <button wire:click="$set('showWorkflowModal', true)" class="inline-flex items-center p-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-gista-900 hover:text-gista-700 rounded-xl active:scale-[0.98] transition-all duration-150" title="Lihat Workflow Log">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </button>
        </div>

        <!-- Role Action Buttons (Approve / Reject / Pending / Bukti Bayar) -->
        <div class="flex items-center space-x-2">
            @if($document->payment_status === 'Paid')
                <a href="{{ route('payment.bukti-bayar', $document->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Bukti Bayar
                </a>
            @endif

            @if(in_array($document->status, ['Waiting for Approval 1', 'Waiting for Checker']) && (auth()->user()?->isChecker() || auth()->user()?->isSpv1()))
                <button wire:click="openAction('reject')" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    Reject
                </button>
                <button wire:click="openAction('approve')" class="inline-flex items-center px-5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    Approve
                </button>
            @endif

            @if($document->status === 'Waiting for Approval 2' && auth()->user()?->isSpv2())
                <button wire:click="openAction('reject')" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    Reject
                </button>
                <button wire:click="openAction('pending')" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    Pending
                </button>
                <button wire:click="openAction('approve')" class="inline-flex items-center px-5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    Approve
                </button>
            @endif

            @if($document->status === 'Approved' && $document->payment_status !== 'Paid' && auth()->user()?->isSpv2())
                <button wire:click="markAsPaid" wire:loading.attr="disabled" class="disabled:opacity-50 inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    <span wire:loading.remove wire:target="markAsPaid">Proses Transfer Dana (Set Paid)</span>
                    <span wire:loading wire:target="markAsPaid" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Memproses...
                    </span>
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Success -->
    @if(session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Display Section: Two Columns (PDF Page 32 & 38) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Left: Metadata Display (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gista-200 shadow-sm p-6">
            <div class="flex items-start justify-between border-b border-gista-100 pb-4">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 font-mono">
                        {{ $document->optg_number }} <span class="text-slate-400">&ndash;</span> <span class="font-sans font-bold text-base text-slate-700">{{ $document->description }}</span>
                    </h2>
                    <p class="text-[11px] text-slate-400 mt-1">Dokumen Pembayaran Eksternal</p>
                </div>
                <span class="w-11 h-11 rounded-xl bg-gista-500 text-white flex items-center justify-center shadow-sm shadow-gista-500/30 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
            </div>

            <div class="divide-y divide-gista-100">
                <div class="grid grid-cols-2 gap-4 py-3.5 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium">Nomor Dokumen Referensi</span>
                        <p class="font-mono font-bold text-slate-800 text-sm mt-1">{{ $document->po_number }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Status Dokumen</span>
                        <div class="mt-1 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold
                                {{ $document->status === 'Approved' ? 'bg-emerald-100 text-emerald-700' :
                                   ($document->status === 'Correction' ? 'bg-rose-100 text-rose-700' :
                                   ($document->status === 'Draft' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-800')) }}">
                                {{ $document->status }}
                            </span>
                            @if($document->payment_status === 'Paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-600 text-white">
                                    Paid
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 py-3.5 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium">Tanggal Pembuatan Dokumen</span>
                        <p class="font-medium text-slate-800 mt-1">{{ $document->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Kelengkapan Dokumen</span>
                        <p class="font-bold mt-1 {{ $document->document_completeness === 'Completed' ? 'text-emerald-600' : ($document->document_completeness === 'Incomplete' ? 'text-rose-600' : 'text-slate-600') }}">
                            {{ $document->document_completeness }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 py-3.5 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium">Tanggal Jatuh Tempo</span>
                        <p class="font-mono font-bold text-slate-800 mt-1">{{ $document->due_date?->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Request Tanggal Perlambatan</span>
                        <p class="font-mono text-slate-800 mt-1">{{ $document->requested_due_date ? $document->requested_due_date->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 py-3.5 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium">Diajukan Oleh</span>
                        <p class="font-semibold text-slate-800 mt-1">{{ $document->creator?->name }} ({{ $document->creator?->nip }})</p>
                        <p class="text-[11px] text-slate-500">{{ $document->creator?->unit_kerja }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Diubah Terakhir Oleh</span>
                        <p class="font-medium text-slate-800 mt-1">{{ $document->last_modified_by ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Attachment Display (1 col) (PDF Page 32) -->
        <div class="bg-gista-50 rounded-2xl border border-gista-200 p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gista-200/70 pb-3">
                <h3 class="text-sm font-bold text-slate-800">Attachment</h3>
                <span class="text-[10px] font-bold uppercase tracking-wider text-gista-700 bg-white border border-gista-200 px-2 py-0.5 rounded-full">{{ $document->attachments->count() }} File</span>
            </div>

            <div class="space-y-3">
                @forelse($document->attachments as $att)
                    <div class="p-3 bg-white rounded-xl border border-gista-200/70 flex items-start justify-between text-xs hover:border-gista-300 transition">
                        <div class="flex items-start space-x-2.5 min-w-0">
                            <span class="p-1.5 rounded-lg bg-rose-50 text-rose-600 font-bold text-[9px] border border-rose-100 shrink-0">PDF</span>
                            <div class="min-w-0">
                                <a href="{{ route('attachments.download', $att->id) }}" class="font-bold text-gista-700 hover:underline block truncate max-w-[160px]">{{ $att->file_name }}</a>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $att->doc_type }}</p>
                                <p class="text-[10px] text-slate-500">{{ $att->notes ?? 'Dokumen invoice' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('attachments.download', $att->id) }}" class="text-gista-600 hover:text-gista-800 shrink-0 p-1 rounded-lg hover:bg-gista-50" title="Unduh">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </a>
                    </div>
                @empty
                    <div class="py-10 text-center text-slate-400 text-xs">
                        <svg class="w-9 h-9 mx-auto text-gista-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Tidak ada dokumen dilampirkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Detail Pembayaran Financial Card (PDF Page 28 & 38) -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-6">
        <h3 class="text-sm font-bold text-slate-800 border-b border-gista-100 pb-3">Detail Pembayaran Vendor</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs bg-slate-50/60 p-4 rounded-xl">
            <div>
                <span class="text-slate-400">Vendor</span>
                <p class="font-bold text-gista-700 mt-0.5">{{ $document->vendor_code }} - {{ $document->vendor_name }}</p>
            </div>
            <div>
                <span class="text-slate-400">Nomor Invoice Vendor</span>
                <p class="font-mono font-bold text-slate-800 mt-0.5">{{ $document->vendor_invoice_number ?? '-' }}</p>
            </div>
            <div>
                <span class="text-slate-400">Tanggal Invoice</span>
                <p class="font-medium text-slate-800 mt-0.5">{{ $document->vendor_invoice_date?->format('d/m/Y') ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs border-t border-gista-100 pt-4">
            <div>
                <span class="text-slate-400">Total Tagihan (DPP + PPN)</span>
                <p class="font-mono font-bold text-slate-800 text-sm mt-0.5">IDR {{ number_format($document->total_dpp + $document->total_ppn, 2, ',', '.') }}</p>
            </div>
            <div>
                <span class="text-slate-400">Rekening DPP Tujuan</span>
                <p class="font-mono font-semibold text-slate-800 mt-0.5">{{ $document->dpp_account_number }}</p>
                <p class="text-[10px] text-slate-400">{{ $document->dpp_account_name }}</p>
            </div>
            <div>
                <span class="text-slate-400">Rekening PPN Tujuan</span>
                <p class="font-mono font-semibold text-slate-800 mt-0.5">{{ $document->ppn_account_number }}</p>
                <p class="text-[10px] text-slate-400">{{ $document->ppn_account_name }}</p>
            </div>
            <div>
                <span class="text-slate-400 font-bold">Total Pembayaran Net</span>
                <p class="font-mono font-extrabold text-gista-600 text-base mt-0.5">IDR {{ number_format($document->total_payment, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Detail Item Pembebanan Table (PDF Page 30) -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gista-100">
            <h3 class="text-sm font-bold text-slate-800">Detail Pembebanan Barang / Jasa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gista-50 text-gista-900 border-b border-gista-200">
                    <tr>
                        <th class="py-2.5 px-3">No. Line PO</th>
                        <th class="py-2.5 px-3">Barang / Item</th>
                        <th class="py-2.5 px-3">Nomor DC</th>
                        <th class="py-2.5 px-3">Tipe Transaksi</th>
                        <th class="py-2.5 px-3">COA</th>
                        <th class="py-2.5 px-3">Cost Center</th>
                        <th class="py-2.5 px-3 text-right">Nilai Tagihan (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gista-100 text-slate-700">
                    @forelse($document->items as $item)
                        <tr class="hover:bg-gista-50">
                            <td class="py-2.5 px-3 font-mono text-slate-500">{{ $item->line_item_po }}</td>
                            <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $item->item_code }} - {{ $item->item_name }}</td>
                            <td class="py-2.5 px-3 font-mono text-gista-600">{{ $item->dc_number }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $item->transaction_type }}</span>
                            </td>
                            <td class="py-2.5 px-3 font-mono">{{ $item->coa }} ({{ $item->sub_coa }})</td>
                            <td class="py-2.5 px-3 font-mono text-slate-600">{{ $item->cost_center }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold">IDR {{ number_format($item->nominal, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada detail item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: WORKFLOW LOG AUDIT TRAIL (PDF Page 39) -->
    @if($showWorkflowModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gista-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="text-sm font-bold text-slate-800">Workflow Log &mdash; {{ $document->optg_number }}</h3>
                    </div>
                    <button wire:click="$set('showWorkflowModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="border border-gista-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gista-50 text-gista-900 border-b border-gista-200">
                            <tr>
                                <th class="py-2.5 px-3">Date and Time</th>
                                <th class="py-2.5 px-3">Name - Eselon</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gista-100">
                            @foreach($document->workflowLogs as $log)
                                <tr class="hover:bg-gista-50">
                                    <td class="py-2.5 px-3 text-slate-500 font-mono">{{ $log->created_at->format('d F Y H:i:s') }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $log->user_name }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold 
                                            {{ $log->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : 
                                               ($log->status === 'Correction' ? 'bg-rose-100 text-rose-800' : 'bg-gista-100 text-gista-800') }}">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-600">{{ $log->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="$set('showWorkflowModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: APPROVE / REJECT ACTION DIALOG -->
    @if($showActionModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                        Konfirmasi {{ $actionType }} Dokumen
                    </h3>
                    <button wire:click="$set('showActionModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    @if($actionType === 'approve' && $document->status === 'Waiting for Approval 2')
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Kelengkapan Dokumen</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center space-x-1.5">
                                    <input type="radio" wire:model="completenessStatus" value="Complete" class="text-gista-600">
                                    <span>Approve Complete (Semua Dokumen Lengkap)</span>
                                </label>
                                <label class="flex items-center space-x-1.5">
                                    <input type="radio" wire:model="completenessStatus" value="Incomplete" class="text-gista-600">
                                    <span>Approve Incomplete (Menunggu Vendor)</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Catatan Tindakan <span class="text-rose-500">*</span></label>
                        <textarea wire:model="actionNotes" rows="3" placeholder="Masukkan alasan atau catatan..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gista-100">
                    <button wire:click="$set('showActionModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Batal</button>
                    <button wire:click="confirmAction" wire:loading.attr="disabled" class="disabled:opacity-50 inline-flex items-center px-5 py-2 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150 flex items-center
                        {{ $actionType === 'reject' ? 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800' : ($actionType === 'pending' ? 'bg-amber-600 hover:bg-amber-700 active:bg-amber-800' : 'bg-gista-600 hover:bg-gista-700 active:bg-gista-800') }}">
                        <span wire:loading.remove wire:target="confirmAction">Konfirmasi {{ ucfirst($actionType) }}</span>
                        <span wire:loading wire:target="confirmAction" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: FAKTUR PAJAK (PDF Page 29, 32) -->
    @if($showFakturModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gista-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-sm font-bold text-slate-800">Detail Faktur Pajak</h3>
                    </div>
                    <button wire:click="$set('showFakturModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gista-50 text-gista-900">
                            <tr>
                                <th class="p-2.5">Jenis</th>
                                <th class="p-2.5">No. Faktur Pajak</th>
                                <th class="p-2.5">Tanggal</th>
                                <th class="p-2.5 text-right">Nilai DPP</th>
                                <th class="p-2.5 text-right">Nilai PPN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gista-100 font-mono">
                            @forelse($document->taxInvoices as $tax)
                                <tr>
                                    <td class="p-2.5 font-sans font-medium text-slate-700">{{ $tax->invoice_type ?? 'Standar' }}</td>
                                    <td class="p-2.5 text-gista-700 font-bold">{{ $tax->invoice_number }}</td>
                                    <td class="p-2.5 text-slate-500">{{ $tax->invoice_date ? \Carbon\Carbon::parse($tax->invoice_date)->format('d/m/Y') : '-' }}</td>
                                    <td class="p-2.5 text-right">IDR {{ number_format($tax->dpp_amount, 2, ',', '.') }}</td>
                                    <td class="p-2.5 text-right font-bold text-emerald-600">IDR {{ number_format($tax->ppn_amount, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-slate-400 font-sans">Tidak ada data faktur pajak terlampir.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="$set('showFakturModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: ESG DETAIL (PDF Page 29, 32) -->
    @if($showEsgModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-sm font-bold text-slate-800">Detail Tagihan ESG</h3>
                    </div>
                    <button wire:click="$set('showEsgModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gista-50 text-gista-900">
                            <tr>
                                <th class="p-2.5">Jenis ESG</th>
                                <th class="p-2.5">Cost Center</th>
                                <th class="p-2.5">Jumlah Pemakaian</th>
                                <th class="p-2.5">Periode</th>
                                <th class="p-2.5 text-right">Nilai Tagihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gista-100 font-mono">
                            @forelse($document->esgRecords as $esg)
                                <tr>
                                    <td class="p-2.5 font-sans font-bold text-gista-700">{{ $esg->esg_type }}</td>
                                    <td class="p-2.5">{{ $esg->cost_center }}</td>
                                    <td class="p-2.5">{{ number_format($esg->usage_amount, 0, ',', '.') }} {{ $esg->unit }}</td>
                                    <td class="p-2.5 text-slate-500">{{ $esg->billing_period ?? '-' }}</td>
                                    <td class="p-2.5 text-right font-bold text-slate-800">IDR {{ number_format($esg->total_amount, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-slate-400 font-sans">Tidak ada data ESG untuk dokumen ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="$set('showEsgModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: JURNAL DETAIL (PDF Page 29, 32) -->
    @if($showJurnalModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gista-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                        <h3 class="text-sm font-bold text-slate-800">Simulasi Jurnal Akuntansi (SAP)</h3>
                    </div>
                    <button wire:click="$set('showJurnalModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gista-50 text-gista-900">
                            <tr>
                                <th class="p-2.5">COA & Deskripsi Akun</th>
                                <th class="p-2.5">Cost Center</th>
                                <th class="p-2.5 text-right">Debit (IDR)</th>
                                <th class="p-2.5 text-right">Kredit (IDR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gista-100 font-mono">
                            <tr>
                                <td class="p-2.5 font-sans">691084 - Biaya Operasional / Pengadaan ({{ $document->po_number }})</td>
                                <td class="p-2.5">{{ $document->cost_center ?? 'C00008600' }}</td>
                                <td class="p-2.5 text-right text-emerald-600 font-bold">IDR {{ number_format($document->total_dpp, 2, ',', '.') }}</td>
                                <td class="p-2.5 text-right text-slate-400">-</td>
                            </tr>
                            @if($document->total_ppn > 0)
                                <tr>
                                    <td class="p-2.5 font-sans">181100 - PPN Masukan Dibayar di Muka</td>
                                    <td class="p-2.5">{{ $document->cost_center ?? 'C00998031' }}</td>
                                    <td class="p-2.5 text-right text-emerald-600 font-bold">IDR {{ number_format($document->total_ppn, 2, ',', '.') }}</td>
                                    <td class="p-2.5 text-right text-slate-400">-</td>
                                </tr>
                            @endif
                            @if($document->total_additional_cost > 0)
                                <tr>
                                    <td class="p-2.5 font-sans">691144 - Biaya Tambahan Pengiriman / Penanganan</td>
                                    <td class="p-2.5">{{ $document->cost_center ?? 'C00998031' }}</td>
                                    <td class="p-2.5 text-right text-emerald-600 font-bold">IDR {{ number_format($document->total_additional_cost, 2, ',', '.') }}</td>
                                    <td class="p-2.5 text-right text-slate-400">-</td>
                                </tr>
                            @endif
                            <tr class="bg-gista-50/50 font-bold">
                                <td class="p-2.5 font-sans">212100 - Hutang Vendor ({{ $document->vendor_name }})</td>
                                <td class="p-2.5">{{ $document->cost_center ?? 'C00998031' }}</td>
                                <td class="p-2.5 text-right text-slate-400">-</td>
                                <td class="p-2.5 text-right text-gista-700">IDR {{ number_format($document->total_payment, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="$set('showJurnalModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
