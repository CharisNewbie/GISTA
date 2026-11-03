<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Title & Action Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">List Pembayaran Tagihan</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola dan monitor pengajuan pembayaran eksternal pengadaan barang & jasa</p>
        </div>
        @if(auth()->user()?->isOperator())
            <a href="{{ route('payment.select-type') }}" class="inline-flex items-center px-4 py-2.5 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Pembayaran Tagihan
            </a>
        @endif
    </div>

    <!-- Search & Quick Filters (PDF Page 5 & 6) -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-4 space-y-4">
        <!-- Search Input Bar & Advanced Search Toggle -->
        <div class="flex items-center space-x-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Pembayaran Tagihan, Keterangan Pembayaran, dan lainnya" class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-gista-500 focus:border-gista-500 outline-none">
            </div>
            <button wire:click="$toggle('showAdvancedSearch')" class="inline-flex items-center px-4 py-2 border border-slate-200 hover:border-slate-300 text-slate-700 hover:bg-gista-50 text-xs font-semibold rounded-xl active:scale-[0.98] transition-all duration-150 space-x-1.5">
                <span>Advanced Search</span>
                <svg class="w-3.5 h-3.5 transform transition-transform {{ $showAdvancedSearch ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <!-- Advanced Filter Collapse Section (PDF Page 6) -->
        @if($showAdvancedSearch)
            <div class="pt-4 border-t border-gista-100 grid grid-cols-1 md:grid-cols-3 gap-3 bg-gista-50/50 p-4 rounded-xl">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">No. Pembayaran Tagihan</label>
                    <input wire:model.live="advOptg" type="text" placeholder="Cari No. Pembayaran Tagihan" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Keterangan Pembayaran</label>
                    <input wire:model.live="advKeterangan" type="text" placeholder="Cari Keterangan" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Vendor</label>
                    <input wire:model.live="advVendor" type="text" placeholder="Cari Vendor" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">No. Dokumen Referensi</label>
                    <input wire:model.live="advPo" type="text" placeholder="Cari No. Referensi" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Berlaku Dari</label>
                    <input wire:model.live="advStartDate" type="date" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Berlaku Sampai</label>
                    <input wire:model.live="advEndDate" type="date" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                </div>
                <div class="md:col-span-3 flex justify-end space-x-2 pt-2">
                    <button wire:click="resetAdvancedSearch" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:border-slate-300 rounded-lg hover:bg-gista-50 active:scale-[0.98] transition-all duration-150">Reset</button>
                    <button wire:click="$refresh" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-white bg-gista-600 hover:bg-gista-700 active:bg-gista-800 rounded-lg shadow-sm hover:shadow active:scale-[0.98] transition-all duration-150">Apply Filter</button>
                </div>
            </div>
        @endif

        <!-- Quick Filter Tabs (PDF Page 6) -->
        <div class="flex items-center space-x-1.5 overflow-x-auto border-t border-gista-100 pt-3 text-xs">
            @php
                $tabs = [
                    'All' => 'All',
                    'Draft' => 'Draft',
                    'Waiting for Approval 1' => 'Waiting for Approval 1',
                    'Waiting for Approval 2' => 'Waiting for Approval 2',
                    'Approved' => 'Approved',
                    'Correction' => 'Correction',
                    'Paid' => 'Paid'
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <button wire:click="setStatusFilter('{{ $key }}')" class="px-3.5 py-1.5 rounded-lg font-semibold transition-all duration-150 whitespace-nowrap active:scale-[0.98] {{ $statusFilter === $key ? 'bg-gista-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Data Table (PDF Page 5) -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-600 border-b border-gista-200 font-semibold">
                        <th class="py-3 px-4 w-12 text-center">No.</th>
                        <th class="py-3 px-4">No. Pembayaran Tagihan</th>
                        <th class="py-3 px-4">Keterangan Pembayaran</th>
                        <th class="py-3 px-4">Tanggal Dokumen</th>
                        <th class="py-3 px-4">No. Dokumen Referensi</th>
                        <th class="py-3 px-4">Vendor</th>
                        <th class="py-3 px-4 text-right">Nilai Total (DPP+PPN)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody wire:loading.class="opacity-60" class="divide-y divide-gista-100 text-slate-700 transition-opacity duration-200">
                    @forelse($documents as $index => $doc)
                        <tr class="hover:bg-gista-50/70 transition {{ $loop->even ? 'bg-gista-50/50' : '' }}">
                            <td class="py-3 px-4 text-center text-slate-400">{{ $documents->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono font-semibold">
                                <a href="{{ route('payment.show', $doc->id) }}" class="text-gista-600 hover:text-gista-800 hover:underline">
                                    {{ $doc->optg_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 max-w-xs truncate" title="{{ $doc->description }}">
                                {{ $doc->description ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $doc->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">
                                {{ $doc->po_number }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ $doc->vendor_name }}
                            </td>
                            <td class="py-3 px-4 text-right font-semibold font-mono text-slate-800">
                                IDR {{ number_format($doc->total_payment, 2, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $isPaid = $doc->payment_status === 'Paid';
                                    $badgeClass = match($doc->status) {
                                        'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'Waiting for Approval 1', 'Waiting for Approval 2', 'Waiting for Checker' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Correction' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'Pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        default => 'bg-gista-50 text-gista-700 border-gista-200'
                                    };
                                    if ($isPaid) {
                                        $badgeClass = 'bg-emerald-600 text-white border-transparent';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $badgeClass }}">
                                    @if($isPaid)
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    @elseif(str_contains($doc->status, 'Waiting'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse mr-1.5"></span>
                                    @elseif($doc->status === 'Approved')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    @endif
                                    {{ $isPaid ? 'Paid' : $doc->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Tidak ada data pembayaran tagihan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="p-4 border-t border-gista-100">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
