<div class="max-w-3xl mx-auto py-6 space-y-6">
    <!-- Action buttons (Hidden on print) -->
    <div class="print:hidden flex items-center justify-between">
        <a href="{{ route('payment.show', $document->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150 space-x-1.5">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Kembali ke Dokumen</span>
        </a>

        <button onclick="window.print()" class="inline-flex items-center px-5 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl space-x-2 shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Bukti Bayar</span>
        </button>
    </div>

    <!-- Official BCA Transfer Slip Container (PDF Page 41) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-md p-8 md:p-12 space-y-8 font-sans print:shadow-none print:border-none print:p-0">
        <!-- BCA Brand Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-5">
            <div class="flex items-center space-x-3">
                <!-- BCA Logo Styling -->
                <div class="w-12 h-12 bg-blue-900 rounded-xl flex items-center justify-center text-white font-black text-xl tracking-tighter">
                    BCA
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-wide text-slate-900 uppercase">PT BANK CENTRAL ASIA TBK</h1>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-0.5">Bukti Transaksi Transfer Dana</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    TRANSFER BERHASIL
                </span>
            </div>
        </div>

        <!-- Transfer Slip Metadata Table (Matching Image247 exact field-value structure) -->
        <div class="space-y-4 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Tanggal</div>
                <div class="col-span-8 font-bold font-mono text-slate-900">: {{ $document->paid_at ? $document->paid_at->format('d/m/Y') : now()->format('d/m/Y') }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Nomor Referensi</div>
                <div class="col-span-8 font-bold font-mono text-slate-900">: {{ $document->optg_number }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Rekening Tujuan Transfer</div>
                <div class="col-span-8 font-mono font-bold text-slate-900">: {{ $document->dpp_account_number ?? '1000027347' }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Nama Penerima</div>
                <div class="col-span-8 font-bold text-slate-900">: {{ strtoupper($document->dpp_account_name ?? $document->vendor_name) }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Bank Penerima</div>
                <div class="col-span-8 font-bold text-slate-900">: {{ $document->dpp_bank_name ?? 'PT. BANK CENTRAL ASIA Tbk.' }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Jumlah</div>
                <div class="col-span-8 font-extrabold font-mono text-base text-gista-800">: {{ $document->currency }} {{ number_format($document->total_payment, 2, ',', '.') }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Terbilang</div>
                <div class="col-span-8 font-bold italic text-slate-800">: {{ $terbilang }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Berita</div>
                <div class="col-span-8 font-mono text-slate-800">: {{ $document->payment_notes ?? $document->po_number }}</div>
            </div>

            <div class="grid grid-cols-12 gap-2 py-1.5 border-b border-gista-100">
                <div class="col-span-4 text-slate-500">Status Pengiriman Dana</div>
                <div class="col-span-8 font-bold text-emerald-700">: Transfer Berhasil</div>
            </div>
        </div>

        <!-- Official Disclaimer Footer (PDF Page 41) -->
        <div class="pt-8 border-t border-slate-200 text-center space-y-1 text-[11px] text-slate-500 italic">
            <p>This form is computer generated message and requires no signature</p>
            <p>Form ini dihasilkan dari hasil cetakan computer dan tidak memerlukan tanda tangan basah pada Form ini</p>
            <div class="pt-4 text-[10px] text-slate-400 not-italic font-mono">
                BCA Logistic Assistant &bull; PAYDUE System &bull; Dokumen Valid
            </div>
        </div>
    </div>
</div>
