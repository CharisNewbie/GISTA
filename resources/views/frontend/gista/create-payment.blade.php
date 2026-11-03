<div class="max-w-6xl mx-auto space-y-6 pb-16">
    <!-- Submit rate-limit error popup (no 429 web page) -->
    @if($submitError)
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
                <p class="text-xs font-bold text-rose-800">Batas Pengajuan Terlampaui</p>
                <p class="text-xs text-slate-600 mt-0.5" x-text="$wire.submitError"></p>
            </div>
            <button @click="show = false" class="text-slate-500 hover:text-slate-600 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Top Step Bar & Navigation (PDF Page 7-9) -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-4 flex items-center justify-between">
        <div class="flex items-center space-x-3.5">
            @php $ringCircumference = 2 * M_PI * 20; $ringOffset = $ringCircumference * (1 - $currentStep / 7); @endphp
            <div class="relative w-14 h-14 shrink-0">
                <svg class="w-14 h-14 -rotate-90" viewBox="0 0 48 48">
                    <circle cx="24" cy="24" r="20" stroke="#e2e8f0" stroke-width="3.5" fill="none" />
                    <circle cx="24" cy="24" r="20" stroke="#0060af" stroke-width="3.5" fill="none" stroke-linecap="round"
                        stroke-dasharray="{{ number_format($ringCircumference, 2, '.', '') }}"
                        stroke-dashoffset="{{ number_format($ringOffset, 2, '.', '') }}" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-sm font-extrabold text-slate-800 leading-none">{{ $currentStep }}</span>
                    <span class="text-[9px] text-slate-500 leading-none mt-0.5">of 7</span>
                </div>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">
                    @switch($currentStep)
                        @case(1) Referensi Pembayaran @break
                        @case(2) Faktur Pajak @break
                        @case(3) Detail Pembebanan @break
                        @case(4) Detail Pembayaran @break
                        @case(5) Alokasi @break
                        @case(6) ESG (Environmental, Social, Governance) @break
                        @case(7) Summary & Lampiran @break
                    @endswitch
                </h3>
                <p class="text-[11px] text-slate-500">
                    @if($currentStep < 7)
                        &rsaquo; Next:
                        @switch($currentStep)
                            @case(1) Faktur Pajak @break
                            @case(2) Detail Pembebanan @break
                            @case(3) Detail Pembayaran @break
                            @case(4) Alokasi @break
                            @case(5) ESG @break
                            @case(6) Summary @break
                        @endswitch
                    @else
                        &rsaquo; Siap diajukan ke approver
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if($currentStep > 1)
                <button type="button" wire:click="prevStep" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                    &larr; Prev
                </button>
            @else
                <a href="{{ route('payment.select-type') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                    &larr; Kembali
                </a>
            @endif

            @if($currentStep < 7)
                <button type="button" wire:click="nextStep" class="inline-flex items-center px-5 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    Lanjut &rarr;
                </button>
            @else
                <button type="button" wire:click="$set('showSubmitModal', true)" class="inline-flex items-center px-5 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    Submit Tagihan &rarr;
                </button>
            @endif
        </div>
    </div>

    <!-- 7-Step Progress Tracker -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm px-4 py-3">
        <div class="flex items-center justify-between px-1">
            @php
                $steps = [
                    1 => 'Referensi',
                    2 => 'Faktur Pajak',
                    3 => 'Pembebanan',
                    4 => 'Pembayaran',
                    5 => 'Alokasi',
                    6 => 'ESG',
                    7 => 'Summary',
                ];
            @endphp
            @foreach($steps as $num => $label)
                @php
                    $stepClass = match(true) {
                        $num < $currentStep => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        $num === $currentStep => 'bg-gista-600 text-white border-gista-600 shadow-sm',
                        default => 'bg-slate-100 text-slate-500 border-slate-200',
                    };
                    $connectorClass = $num < $currentStep ? 'bg-emerald-200' : ($num === $currentStep ? 'bg-gista-600' : 'bg-slate-200');
                @endphp
                <div class="shrink-0 flex flex-col items-center">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border z-10 {{ $stepClass }}">
                        {{ $num }}
                    </div>
                    <span class="mt-0.5 text-[9px] font-medium leading-tight text-center w-16 px-0.5 {{ $num === $currentStep ? 'text-gista-700 font-bold' : 'text-slate-500' }}">{{ $label }}</span>
                </div>
                @if($num < 7)
                    <div class="flex-1 h-[1.5px] mx-1.5 {{ $connectorClass }} shrink-0"></div>
                @endif
            @endforeach
        </div>
    </div>

    @if($isEditMode)
        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-xl flex items-center justify-between">
            <span><strong>Mode Perbaikan:</strong> Anda sedang mengedit pengajuan yang dikembalikan. Perbaiki data lalu kirim ulang.</span>
        </div>
    @endif

    <!-- Flash message -->
    @if(session()->has('error'))
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- STEP 1: REFERENSI PEMBAYARAN (PDF Page 7-8) -->
    @if($currentStep === 1)
        <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-6">
            <h4 class="text-sm font-bold text-slate-800 border-b border-gista-100 pb-3">Referensi Pembayaran</h4>

            <!-- PO Reference Search & Auto-Fill Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Dokumen Referensi <span class="text-rose-500">*</span></label>
                    <div class="flex space-x-2">
                        <input wire:model="poNumber" type="text" placeholder="POST25001876" class="flex-1 px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-gista-500 focus:border-gista-500">
                        <button wire:click="checkPo" wire:loading.attr="disabled" type="button" class="disabled:opacity-50 inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                            <span wire:loading.remove wire:target="checkPo">Check</span>
                            <span wire:loading wire:target="checkPo" class="flex items-center">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            </span>
                        </button>
                    </div>
                    @if($poFeedback)
                        <span class="inline-flex items-start gap-1 mt-1 text-[11px] font-semibold {{ $poChecked ? 'text-emerald-600' : 'text-rose-600' }}">
                            <svg class="w-3.5 h-3.5 mt-px shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($poChecked)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                @endif
                            </svg>
                            {{ $poFeedback }}
                        </span>
                    @endif
                    <span class="inline-block mt-1 text-[11px] text-gista-600 font-semibold">PO Non Termin</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data Vendor</label>
                    <input type="text" readonly value="{{ $vendorCode }} - {{ $vendorName }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Dokumen</label>
                    <input wire:model="docDescription" type="text" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mata Uang</label>
                    <input type="text" readonly value="{{ $currency }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                </div>
            </div>

            <!-- DC Selection Table -->
            <div class="pt-4 border-t border-gista-100">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="text-xs font-bold text-slate-700">Pilih item yang akan dibayar</h5>
                    <label class="cursor-pointer px-3 py-1.5 border border-gista-500 text-gista-600 hover:bg-gista-50 rounded-xl text-xs font-medium transition inline-flex items-center gap-1.5">
                        <input type="file" wire:model="dcFile" accept=".txt,.csv" class="hidden">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        <span wire:loading.remove wire:target="dcFile">Select by File</span>
                        <span wire:loading wire:target="dcFile">Membaca file...</span>
                    </label>
                </div>

                <div class="border border-gista-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gista-50 text-gista-900 border-b border-gista-200">
                            <tr>
                                <th class="py-2.5 px-3">Nomor DC</th>
                                <th class="py-2.5 px-3">Kode PPN</th>
                                <th class="py-2.5 px-3 text-right">Total Nominal DC</th>
                                <th class="py-2.5 px-3 text-center">Status DC</th>
                                <th class="py-2.5 px-3 text-center">Pilih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gista-100">
                            @foreach($dcItems as $item)
                                <tr class="hover:bg-gista-50">
                                    <td class="py-2.5 px-3 font-mono font-semibold text-gista-600">{{ $item['dc_number'] }}</td>
                                    <td class="py-2.5 px-3">{{ $item['ppn_code'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-medium">IDR {{ number_format($item['nominal'], 2, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            {{ $item['status_dc'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <input type="checkbox" wire:model.live="selectedDc" value="{{ $item['dc_number'] }}" class="rounded text-gista-600 focus:ring-gista-500 w-4 h-4">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gista-50/50 font-bold border-t border-slate-200">
                            <tr>
                                <td colspan="2" class="py-2.5 px-3 text-right text-slate-600">Total Terpilih:</td>
                                <td class="py-2.5 px-3 text-right font-mono text-gista-700">IDR {{ number_format($this->totalDpp, 2, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- STEP 2: FAKTUR PAJAK (PDF Page 9-11) -->
    @if($currentStep === 2)
        <div class="space-y-6">
            <!-- Balance Indicator Cards (PDF Page 9) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-2xl p-6 border {{ abs($this->selisihDpp) > 0.01 ? 'bg-rose-50/60 border-rose-200 text-rose-800' : 'bg-emerald-50/60 border-emerald-200 text-emerald-800' }}">
                    <p class="text-xs font-semibold uppercase tracking-wider">Selisih DPP</p>
                    <p class="text-2xl font-extrabold font-mono mt-1">
                        IDR {{ number_format($this->selisihDpp, 2, ',', '.') }}
                    </p>
                    <p class="text-[11px] mt-1 opacity-80">
                        {{ abs($this->selisihDpp) > 0.01 ? 'Perhatian: Selisih DPP harus 0 untuk dapat melanjutkan.' : 'DPP Balance (Sesuai)' }}
                    </p>
                </div>

                <div class="rounded-2xl p-6 bg-gista-50/60 border border-gista-200 text-gista-800">
                    <p class="text-xs font-semibold uppercase tracking-wider">Total PPN</p>
                    <p class="text-2xl font-extrabold font-mono mt-1">
                        IDR {{ number_format($this->totalTaxPpn, 2, ',', '.') }}
                    </p>
                    <p class="text-[11px] mt-1 opacity-80">Total PPN atas seluruh faktur pajak terdaftar</p>
                </div>
            </div>

            <!-- List Faktur Pajak -->
            <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gista-100 pb-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">List Faktur Pajak</h4>
                        <p class="text-xs text-slate-500">Nomor Dokumen Referensi: {{ $poNumber }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="openFakturUpload" type="button" class="inline-flex items-center px-3.5 py-1.5 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                            Upload
                        </button>
                        <button wire:click="$set('showTaxModal', true)" type="button" class="inline-flex items-center px-4 py-1.5 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                            + Add Faktur Pajak
                        </button>
                    </div>
                </div>

                <div class="border border-gista-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gista-50 text-gista-900 border-b border-gista-200">
                            <tr>
                                <th class="py-2.5 px-3">No.</th>
                                <th class="py-2.5 px-3">Jenis</th>
                                <th class="py-2.5 px-3">No. Faktur Pajak</th>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3 text-right">Nilai Harga Jual IDR</th>
                                <th class="py-2.5 px-3 text-right">Nilai PPN IDR</th>
                                <th class="py-2.5 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gista-100">
                            @forelse($taxInvoices as $idx => $tax)
                                <tr class="hover:bg-gista-50">
                                    <td class="py-2.5 px-3 text-slate-500">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold">{{ $tax['type'] }}</span></td>
                                    <td class="py-2.5 px-3 font-mono font-semibold text-slate-800">{{ $tax['invoice_number'] }}</td>
                                    <td class="py-2.5 px-3 text-slate-500">{{ $tax['invoice_date'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono">IDR {{ number_format($tax['dpp'], 2, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono">IDR {{ number_format($tax['ppn'], 2, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <button @click="confirm('Hapus faktur pajak ini?') && $wire.removeTaxInvoice({{ $idx }})" class="p-1 text-rose-500 hover:bg-rose-50 rounded">
                                            <x-icon name="trash" />
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500">Belum ada faktur pajak diinput. Silakan klik + Add Faktur Pajak.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- STEP 3: DETAIL PEMBEBANAN (PDF Page 12-18) -->
    @if($currentStep === 3)
        <div class="space-y-6">
            <!-- Total Tagihan Summary Bar -->
            <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-500">Total Tagihan DPP</span>
                    <p class="font-mono font-bold text-slate-800 text-sm">IDR {{ number_format($this->totalDpp, 2, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Total Nilai PPN IDR</span>
                    <p class="font-mono font-bold text-slate-800 text-sm">IDR {{ number_format($this->totalTaxPpn, 2, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Total Biaya Tambahan</span>
                    <p class="font-mono font-bold text-slate-800 text-sm">IDR {{ number_format($this->totalAdditionalCost, 2, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Total Tagihan Keseluruhan</span>
                    <p class="font-mono font-bold text-gista-600 text-base">IDR {{ number_format($this->totalPayment, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Tabs: Detail Pembebanan vs Pembebanan Tambahan -->
            <div class="bg-white rounded-2xl border border-gista-200 shadow-sm overflow-hidden">
                <div class="flex border-b border-gista-100 p-2 space-x-2">
                    <button wire:click="$set('pembebananTab', 'items')" class="px-4 py-2 text-xs font-bold rounded-xl active:scale-[0.98] transition-all duration-150 {{ $pembebananTab === 'items' ? 'bg-gista-50 text-gista-700 shadow-sm' : 'text-slate-600 hover:bg-gista-50' }}">
                        Detail Pembebanan
                    </button>
                    <button wire:click="$set('pembebananTab', 'additional')" class="px-4 py-2 text-xs font-bold rounded-xl active:scale-[0.98] transition-all duration-150 {{ $pembebananTab === 'additional' ? 'bg-gista-50 text-gista-700 shadow-sm' : 'text-slate-600 hover:bg-gista-50' }}">
                        Pembebanan Tambahan
                    </button>
                </div>

                <div class="p-4">
                    @if($pembebananTab === 'items')
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gista-50 text-gista-900 border-b border-gista-200">
                                    <tr>
                                        <th class="py-2.5 px-3">No. Line PO</th>
                                        <th class="py-2.5 px-3">No. DC</th>
                                        <th class="py-2.5 px-3">Tipe Transaksi</th>
                                        <th class="py-2.5 px-3">SLID</th>
                                        <th class="py-2.5 px-3">COA / Sub COA</th>
                                        <th class="py-2.5 px-3">Cost Center</th>
                                        <th class="py-2.5 px-3 text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gista-100">
                                    @foreach($dcItems as $item)
                                        @if(in_array($item['dc_number'], $selectedDc))
                                            <tr>
                                                <td class="py-2.5 px-3 text-slate-500">{{ $item['line_item_po'] }}</td>
                                                <td class="py-2.5 px-3 font-mono text-gista-600">{{ $item['dc_number'] }}</td>
                                                <td class="py-2.5 px-3">
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $item['transaction_type'] }}</span>
                                                </td>
                                                <td class="py-2.5 px-3 font-mono text-slate-500">{{ $item['slid'] }}</td>
                                                <td class="py-2.5 px-3">
                                                    <span class="font-semibold">{{ $item['coa'] }}</span> ({{ $item['sub_coa'] }})<br>
                                                    <span class="text-[10px] text-slate-500">{{ $item['coa_name'] }}</span>
                                                </td>
                                                <td class="py-2.5 px-3 font-mono text-slate-600">{{ $item['cost_center'] }}</td>
                                                <td class="py-2.5 px-3 text-right font-mono font-semibold">IDR {{ number_format($item['nominal'], 2, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- Pembebanan Tambahan Tab (PDF Page 16-17) -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <p class="text-xs text-slate-500">Biaya di luar invoice (biaya administrasi, keterlambatan, ongkos kirim, potongan, atau denda).</p>
                                <button wire:click="$set('showAdditionalModal', true)" type="button" class="inline-flex items-center px-3.5 py-1.5 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                                    + Tambah Pembebanan
                                </button>
                            </div>

                            <table class="w-full text-left text-xs border border-gista-200 rounded-xl overflow-hidden">
                                <thead class="bg-gista-50 text-gista-900">
                                    <tr>
                                        <th class="py-2 px-3">Jenis</th>
                                        <th class="py-2 px-3">COA</th>
                                        <th class="py-2 px-3">Cost Center</th>
                                        <th class="py-2 px-3 text-right">Nilai Pembebanan</th>
                                        <th class="py-2 px-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gista-100">
                                    @forelse($additionalCosts as $idx => $cost)
                                        <tr>
                                            <td class="py-2 px-3 font-medium">{{ $cost['type'] }}</td>
                                            <td class="py-2 px-3 font-mono">{{ $cost['coa'] }}</td>
                                            <td class="py-2 px-3 font-mono">{{ $cost['cost_center'] }}</td>
                                            <td class="py-2 px-3 text-right font-mono font-semibold">IDR {{ number_format($cost['amount'], 2, ',', '.') }}</td>
                                            <td class="py-2 px-3 text-center">
                                                <button @click="confirm('Hapus pembebanan tambahan ini?') && $wire.removeAdditionalCost({{ $idx }})" class="text-rose-500 hover:text-rose-700">Hapus</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-slate-500">Belum ada pembebanan tambahan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- STEP 4: DETAIL PEMBAYARAN (PDF Page 18-20) -->
    @if($currentStep === 4)
        <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-6">
            <h4 class="text-sm font-bold text-slate-800 border-b border-gista-100 pb-3">Detail Pembayaran</h4>

            <!-- Jatuh Tempo Section -->
            <div class="p-4 bg-slate-50 rounded-xl space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">Tanggal Jatuh Tempo <span class="text-rose-500">*</span></label>
                        <p class="text-[11px] text-slate-500">Secara default tagihan dibayarkan H+1 atau H+2</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <label class="text-xs font-semibold text-slate-600">Request Percepatan/Perlambatan</label>
                        <input type="checkbox" wire:model.live="hasDueDateRequest" class="w-4 h-4 rounded text-gista-600 focus:ring-gista-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Tanggal Jatuh Tempo Sistem</label>
                        <input type="date" wire:model="dueDate" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white">
                    </div>
                    @if($hasDueDateRequest)
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Tanggal Baru Yang Diajukan</label>
                            <input type="date" wire:model="requestedDueDate" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Vendor Invoice Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Invoice Vendor <span class="text-rose-500">*</span></label>
                    <input wire:model="vendorInvoiceNo" type="text" placeholder="Masukkan nomor invoice vendor" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Invoice Vendor <span class="text-rose-500">*</span></label>
                    <input wire:model="vendorInvoiceDate" type="date" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                </div>
            </div>

            <!-- Bank Accounts (DPP & PPN) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 border border-slate-200 rounded-xl space-y-2">
                    <span class="text-xs font-bold text-slate-800">Rekening DPP <span class="text-rose-500">*</span></span>
                    <input type="text" wire:model="dppAccount" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg font-mono">
                    <p class="text-[11px] text-slate-500">Nama: {{ $dppAccountName }} ({{ $dppBank }})</p>
                </div>
                <div class="p-4 border border-slate-200 rounded-xl space-y-2">
                    <span class="text-xs font-bold text-slate-800">Rekening PPN <span class="text-rose-500">*</span></span>
                    <input type="text" wire:model="ppnAccount" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg font-mono">
                    <p class="text-[11px] text-slate-500">Nama: {{ $ppnAccountName }} ({{ $ppnBank }})</p>
                </div>
            </div>

            <!-- Notes -->
            <div x-data="{ counter: 0 }" x-init="counter = $el.querySelector('input').value.length" class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Pembayaran <span class="text-rose-500">*</span></label>
                <input wire:model="paymentNotes" type="text" maxlength="100" x-on:input="counter = $event.target.value.length" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                <p class="text-[10px] text-slate-500 mt-1" x-text="counter + ' / 100 karakter'"></p>
            </div>
        </div>
    @endif

    <!-- STEP 5: ALOKASI (PDF Page 21-24) -->
    @if($currentStep === 5)
        <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Alokasi Biaya Antar Unit Kerja</h4>
                    <p class="text-xs text-slate-500">Membagi pembebanan biaya ke beberapa cost center unit kerja</p>
                </div>
                <button wire:click="$set('showAllocationModal', true)" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    + Add Alokasi
                </button>
            </div>

            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center justify-between">
                <span>Total Nilai Tagihan: IDR {{ number_format($this->totalPayment, 2, ',', '.') }}</span>
                <span class="font-bold">Status Alokasi: Balance</span>
            </div>

            <table class="w-full text-left text-xs border border-gista-200 rounded-xl overflow-hidden">
                <thead class="bg-gista-50 text-gista-900">
                    <tr>
                        <th class="py-2.5 px-3">Item Alokasi</th>
                        <th class="py-2.5 px-3">Cost Center</th>
                        <th class="py-2.5 px-3">Keterangan</th>
                        <th class="py-2.5 px-3 text-right">Nilai Alokasi</th>
                        <th class="py-2.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gista-100">
                    @forelse($allocations as $idx => $alloc)
                        <tr>
                            <td class="py-2.5 px-3 font-mono text-gista-600">{{ $alloc['item'] }}</td>
                            <td class="py-2.5 px-3 font-mono">{{ $alloc['cost_center'] }}</td>
                            <td class="py-2.5 px-3 text-slate-500">{{ $alloc['notes'] }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold">IDR {{ number_format($alloc['amount'], 2, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <button @click="confirm('Hapus alokasi biaya ini?') && $wire.removeAllocation({{ $idx }})" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-500">Belum ada alokasi biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- STEP 6: ESG (PDF Page 24-27) -->
    @if($currentStep === 6)
        <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Tagihan ESG (Environmental, Social, and Governance)</h4>
                    <p class="text-xs text-slate-500">Pengisian data konsumsi energi untuk pelaporan keberlanjutan perusahaan</p>
                </div>
                <button wire:click="$set('showEsgModal', true)" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                    + Add ESG
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <p class="text-[11px] font-bold text-slate-500 uppercase">Total Tagihan ESG</p>
                    <p class="text-xl font-bold font-mono text-slate-800 mt-1">IDR {{ number_format(array_sum(array_column($esgRecords, 'total_amount')), 2, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-xl bg-gista-50 border border-gista-200">
                    <p class="text-[11px] font-bold text-gista-600 uppercase">Status Validasi ESG</p>
                    <p class="text-xl font-bold font-mono text-gista-800 mt-1">Balance (Sesuai)</p>
                </div>
            </div>

            <table class="w-full text-left text-xs border border-gista-200 rounded-xl overflow-hidden">
                <thead class="bg-gista-50 text-gista-900">
                    <tr>
                        <th class="py-2.5 px-3">Jenis ESG</th>
                        <th class="py-2.5 px-3">Cost Center</th>
                        <th class="py-2.5 px-3">Jumlah Pemakaian</th>
                        <th class="py-2.5 px-3">Harga Satuan</th>
                        <th class="py-2.5 px-3 text-right">Nilai Tagihan</th>
                        <th class="py-2.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gista-100">
                    @forelse($esgRecords as $idx => $esg)
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-gista-700">{{ $esg['esg_type'] }}</td>
                            <td class="py-2.5 px-3 font-mono">{{ $esg['cost_center'] }}</td>
                            <td class="py-2.5 px-3 font-mono">{{ number_format($esg['usage_amount'], 0, ',', '.') }} {{ $esg['unit'] }}</td>
                            <td class="py-2.5 px-3 font-mono">IDR {{ number_format($esg['unit_price'], 2, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold">IDR {{ number_format($esg['total_amount'], 2, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <button @click="confirm('Hapus data ESG ini?') && $wire.removeEsg({{ $idx }})" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-500">Belum ada data ESG.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- STEP 7: SUMMARY & SUBMIT (PDF Page 27-31) -->
    @if($currentStep === 7)
        <div class="space-y-6">
            <!-- Header Summary Box -->
            <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <div>
                        <span class="text-xs font-mono font-bold text-gista-600">OPTG (Nomor akan digenerate otomatis)</span>
                        <h3 class="text-base font-bold text-slate-800 mt-0.5">{{ $docDescription }}</h3>
                    </div>

                    <!-- Quick View Modals Triggers (PDF Page 28-29) -->
                    <div class="flex space-x-2">
                        <button wire:click="$set('showQuickFakturModal', true)" type="button" class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gista-50 border border-gista-400 text-gista-600 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                            Faktur Pajak
                        </button>
                        <button wire:click="$set('showQuickEsgModal', true)" type="button" class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gista-50 border border-gista-400 text-gista-600 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                            ESG
                        </button>
                        <button wire:click="$set('showQuickJurnalModal', true)" type="button" class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-gista-50 border border-gista-400 text-gista-600 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">
                            Jurnal
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500">Nomor PO:</span>
                        <p class="font-mono font-bold text-slate-700">{{ $poNumber }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Vendor:</span>
                        <p class="font-bold text-slate-700">{{ $vendorName }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Tanggal Jatuh Tempo:</span>
                        <p class="font-mono font-bold text-slate-700">{{ $dueDate }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Total Pembayaran:</span>
                        <p class="font-mono font-bold text-gista-600 text-sm">IDR {{ number_format($this->totalPayment, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Attachments Section (PDF Page 28, 30) -->
            <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Lampiran Dokumen</h4>
                        <p class="text-xs text-slate-500">Invoice vendor & dokumen pendukung lainnya</p>
                    </div>
                    <button wire:click="$set('showAttachModal', true)" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                        + Add Attachment
                    </button>
                </div>

                <div class="divide-y divide-gista-100">
                    @forelse($attachments as $att)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-2">
                                <x-icon name="file" class="w-5 h-5 text-rose-500" />
                                <div>
                                    <p class="font-bold text-slate-800">{{ $att['name'] }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $att['type'] }} &bull; {{ $att['notes'] }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-emerald-600 font-semibold">Tergugah</span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-500 text-xs">
                            Belum ada dokumen dilampirkan. Klik + Add Attachment untuk melampirkan invoice vendor.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD FAKTUR PAJAK (PDF Page 10) -->
    @if($showTaxModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Add Faktur Pajak</h3>
                    <button wire:click="$set('showTaxModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis Faktur Pajak</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center space-x-1.5">
                                <input type="radio" wire:model="taxType" value="Standar" class="text-gista-600">
                                <span>Standar (16/17 digit)</span>
                            </label>
                            <label class="flex items-center space-x-1.5">
                                <input type="radio" wire:model="taxType" value="Dipersamakan" class="text-gista-600">
                                <span>Dipersamakan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Faktur Pajak <span class="text-rose-500">*</span></label>
                        <input wire:model="taxInvoiceNumber" type="text" placeholder="1234-5678-9101-11213" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Faktur Pajak</label>
                        <input wire:model="taxInvoiceDate" type="date" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nilai Harga Jual IDR (DPP) <span class="text-rose-500">*</span></label>
                            <input wire:model="taxDpp" type="number" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nilai PPN IDR <span class="text-rose-500">*</span></label>
                            <input wire:model="taxPpn" type="number" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gista-100">
                    <button wire:click="$set('showTaxModal', false)" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Batal</button>
                    <button wire:click="addTaxInvoice" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">Submit</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: SUBMIT TAGIHAN (PILIH APPROVER) (PDF Page 30-31) -->
    @if($showSubmitModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="text-center pb-2">
                    <div class="w-12 h-12 rounded-full bg-gista-100 text-gista-600 mx-auto flex items-center justify-center mb-2">
                        <x-icon name="clipboard-check" class="w-6 h-6" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Pilih Approver</h3>
                    <p class="text-xs text-slate-500">Pilih supervisor unit kerja untuk memeriksa data tagihan pembayaran</p>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Supervisor Unit Kerja <span class="text-rose-500">*</span></label>
                        <select wire:model="selectedSpv1Id" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white">
                            @foreach($supervisors as $spv)
                                <option value="{{ $spv->id }}">{{ $spv->name }} ({{ $spv->eselon }} - {{ $spv->unit_kerja }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model.live="withChecker" class="rounded text-gista-600 focus:ring-gista-500 w-4 h-4">
                            <span class="font-bold text-slate-700">With Checker</span>
                        </label>
                        <p class="text-[10px] text-slate-500 ml-6">Pengecekan ganda oleh reviewer eselon di bawah supervisor</p>
                    </div>

                    @if($withChecker)
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Checker <span class="text-rose-500">*</span></label>
                            <select wire:model="selectedCheckerId" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white">
                                @foreach($checkers as $chk)
                                    <option value="{{ $chk->id }}">{{ $chk->name }} ({{ $chk->eselon }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Catatan</label>
                        <textarea wire:model="submitNotes" rows="2" maxlength="100" placeholder="Catatan pengajuan pembayaran" class="w-full px-3 py-2 border border-slate-200 rounded-xl"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gista-100">
                    <button wire:click="$set('showSubmitModal', false)" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Batal</button>
                    <button wire:click="submitPayment" wire:loading.attr="disabled" type="button" class="disabled:opacity-50 inline-flex items-center px-5 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150 flex items-center">
                        <span wire:loading.remove wire:target="submitPayment">{{ $isEditMode ? 'Ajukan Ulang' : 'Kirim Pengajuan' }}</span>
                        <span wire:loading wire:target="submitPayment" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Mengirim...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD ATTACHMENT (PDF Page 30) -->
    @if($showAttachModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Tambah Dokumen</h3>
                    <button wire:click="$set('showAttachModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Upload File <span class="text-rose-500">*</span></label>
                        <input type="file" wire:model="attachFile" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gista-50 file:text-gista-700 hover:file:bg-gista-100">
                        <p class="text-[10px] text-slate-500 mt-1">Format: DOC, XLS, XLSX, PDF, JPG, PNG (Max 10MB)</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis Dokumen <span class="text-rose-500">*</span></label>
                        <select wire:model="attachType" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white">
                            <option value="Invoice Vendor">Invoice Vendor</option>
                            <option value="Faktur Pajak">Faktur Pajak</option>
                            <option value="Dokumen Pendukung Lainnya">Dokumen Pendukung Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <input type="text" wire:model="attachNotes" maxlength="100" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gista-100">
                    <button wire:click="$set('showAttachModal', false)" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Batal</button>
                    <button wire:click="addAttachment" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">Upload</button>
                </div>
            </div>
        </div>
    @endif

    <!-- QUICK MODALS: FAKTUR PAJAK (PDF Page 29) -->
    @if($showQuickFakturModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Summary Faktur Pajak</h3>
                    <button wire:click="$set('showQuickFakturModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <table class="w-full text-left text-xs">
                    <thead class="bg-gista-50 text-gista-900">
                        <tr>
                            <th class="p-2">No. Faktur Pajak</th>
                            <th class="p-2">Tanggal</th>
                            <th class="p-2 text-right">Nilai DPP</th>
                            <th class="p-2 text-right">Nilai PPN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gista-100 font-mono">
                        @foreach($taxInvoices as $tax)
                            <tr>
                                <td class="p-2 text-gista-600 font-bold">{{ $tax['invoice_number'] }}</td>
                                <td class="p-2">{{ $tax['invoice_date'] }}</td>
                                <td class="p-2 text-right">IDR {{ number_format($tax['dpp'], 2, ',', '.') }}</td>
                                <td class="p-2 text-right">IDR {{ number_format($tax['ppn'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-end pt-2">
                    <button wire:click="$set('showQuickFakturModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD PEMBEBANAN TAMBAHAN (PDF Page 16) -->
    @if($showAdditionalModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Tambah Pembebanan Tambahan</h3>
                    <button wire:click="$set('showAdditionalModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis Pembebanan</label>
                        <select wire:model="addCostType" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white">
                            <option value="Biaya Tambahan (Db)">Biaya Tambahan (Db)</option>
                            <option value="Potongan (Kr)">Potongan (Kr)</option>
                            <option value="Denda (Kr)">Denda (Kr)</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">COA</label>
                            <input wire:model="addCostCoa" type="text" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Cost Center</label>
                            <input wire:model="addCostCostCenter" type="text" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nominal (IDR) <span class="text-rose-500">*</span></label>
                        <input wire:model="addCostAmount" type="number" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-3 border-t border-gista-100">
                    <button wire:click="$set('showAdditionalModal', false)" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Batal</button>
                    <button wire:click="addAdditionalCost" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD ALOKASI (PDF Page 21) -->
    @if($showAllocationModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Tambah Alokasi Biaya</h3>
                    <button wire:click="$set('showAllocationModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Item Alokasi</label>
                        <input wire:model="allocItem" type="text" placeholder="DC25001876-0001 (Barang Biaya)" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Cost Center Tujuan</label>
                        <input wire:model="allocCostCenter" type="text" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nilai Alokasi (IDR) <span class="text-rose-500">*</span></label>
                        <input wire:model="allocAmount" type="number" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Keterangan</label>
                        <input wire:model="allocNotes" type="text" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-3 border-t border-gista-100">
                    <button wire:click="$set('showAllocationModal', false)" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Batal</button>
                    <button wire:click="addAllocation" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD ESG (PDF Page 24) -->
    @if($showEsgModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Tambah Data ESG</h3>
                    <button wire:click="$set('showEsgModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis ESG</label>
                        <select wire:model="esgType" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white">
                            <option value="Listrik">Listrik</option>
                            <option value="Air">Air</option>
                            <option value="Bahan Bakar">Bahan Bakar</option>
                            <option value="Angkutan">Angkutan</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Cost Center</label>
                            <input wire:model="esgCostCenter" type="text" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Satuan (Unit)</label>
                            <input wire:model="esgUnit" type="text" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Jumlah Pemakaian</label>
                            <input wire:model="esgUsage" type="number" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Harga Satuan (IDR)</label>
                            <input wire:model="esgUnitPrice" type="number" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Total Tagihan (IDR)</label>
                            <input wire:model="esgTotal" type="number" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Periode Pemakaian</label>
                            <input wire:model="esgPeriod" type="text" placeholder="07/2024" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-3 border-t border-gista-100">
                    <button wire:click="$set('showEsgModal', false)" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Batal</button>
                    <button wire:click="addEsg" type="button" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- QUICK MODALS: ESG SUMMARY (PDF Page 29) -->
    @if($showQuickEsgModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Summary ESG</h3>
                    <button wire:click="$set('showQuickEsgModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <table class="w-full text-left text-xs">
                    <thead class="bg-gista-50 text-gista-900">
                        <tr>
                            <th class="p-2">Jenis ESG</th>
                            <th class="p-2">Cost Center</th>
                            <th class="p-2">Pemakaian</th>
                            <th class="p-2 text-right">Nilai Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gista-100 font-mono">
                        @foreach($esgRecords as $esg)
                            <tr>
                                <td class="p-2 text-gista-600 font-bold font-sans">{{ $esg['esg_type'] }}</td>
                                <td class="p-2">{{ $esg['cost_center'] }}</td>
                                <td class="p-2">{{ number_format($esg['usage_amount'], 0, ',', '.') }} {{ $esg['unit'] }}</td>
                                <td class="p-2 text-right">IDR {{ number_format($esg['total_amount'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-end pt-2">
                    <button wire:click="$set('showQuickEsgModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- QUICK MODALS: JURNAL SUMMARY (PDF Page 29) -->
    @if($showQuickJurnalModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Simulasi Jurnal Akuntansi</h3>
                    <button wire:click="$set('showQuickJurnalModal', false)" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <table class="w-full text-left text-xs">
                    <thead class="bg-gista-50 text-gista-900">
                        <tr>
                            <th class="p-2">COA & Deskripsi</th>
                            <th class="p-2">Cost Center</th>
                            <th class="p-2 text-right">Debit (IDR)</th>
                            <th class="p-2 text-right">Kredit (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gista-100 font-mono">
                        <tr>
                            <td class="p-2 font-sans">691084 - Biaya Angkutan Barang Logistik</td>
                            <td class="p-2">C00008600</td>
                            <td class="p-2 text-right text-emerald-600 font-bold">IDR {{ number_format($this->totalDpp, 2, ',', '.') }}</td>
                            <td class="p-2 text-right text-slate-500">-</td>
                        </tr>
                        @if($this->totalTaxPpn > 0)
                            <tr>
                                <td class="p-2 font-sans">181100 - PPN Masukan</td>
                                <td class="p-2">C00998031</td>
                                <td class="p-2 text-right text-emerald-600 font-bold">IDR {{ number_format($this->totalTaxPpn, 2, ',', '.') }}</td>
                                <td class="p-2 text-right text-slate-500">-</td>
                            </tr>
                        @endif
                        <tr class="bg-gista-50/50 font-bold">
                            <td class="p-2 font-sans">212100 - Hutang Pembayaran Vendor ({{ $vendorName }})</td>
                            <td class="p-2">C00998031</td>
                            <td class="p-2 text-right text-slate-500">-</td>
                            <td class="p-2 text-right text-gista-700">IDR {{ number_format($this->totalPayment, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex justify-end pt-2">
                    <button wire:click="$set('showQuickJurnalModal', false)" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-gista-50 text-slate-700 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
