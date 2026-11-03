<div class="max-w-7xl mx-auto space-y-6 pb-16">
    <!-- Header (PDF Page 40) -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Report Jatuh Tempo</h2>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring jadwal pembayaran tagihan dan tracking status bayar per dokumen OPTG</p>
        </div>
        <div class="flex items-center space-x-3">
            <button wire:click="exportCsv" class="inline-flex items-center px-4 py-2 bg-gista-600 hover:bg-gista-700 text-white text-xs font-semibold rounded-xl shadow-sm active:scale-[0.98] transition-all duration-150 space-x-1.5">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Download CSV</span>
            </button>
        </div>
    </div>

    <!-- Search & Filters -->
    @if($authError)
        <div class="bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs font-semibold text-rose-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span>{{ $authError }}</span>
        </div>
    @endif
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm p-4 space-y-4">
        <div class="flex items-center space-x-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Dokumen OPTG, Vendor, PO..." class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-gista-500 outline-none">
            </div>
            <select wire:model.live="statusFilter" class="px-3 py-2 text-xs border border-slate-200 rounded-xl bg-white text-slate-700">
                <option value="All">Semua Status</option>
                <option value="Approved">Approved</option>
                <option value="Waiting for Approval 1">Waiting for Approval 1</option>
                <option value="Waiting for Approval 2">Waiting for Approval 2</option>
                <option value="Paid">Paid</option>
            </select>
        </div>
    </div>

    <!-- Report Table (PDF Page 40 - Image242) -->
    <div class="bg-white rounded-2xl border border-gista-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gista-50 text-gista-900 border-b border-gista-200 font-semibold">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No.</th>
                        <th class="py-3 px-4">No. Pembayaran Tagihan</th>
                        <th class="py-3 px-4">Status Dokumen</th>
                        <th class="py-3 px-4">No. Dokumen Jurnal</th>
                        <th class="py-3 px-4">Tgl Posting Jurnal</th>
                        <th class="py-3 px-4 text-center">Tgl Jatuh Tempo</th>
                        <th class="py-3 px-4 text-center">Mata Uang</th>
                        <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                        <th class="py-3 px-4 text-center">Status Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gista-100 text-slate-700">
                    @forelse($documents as $idx => $doc)
                        <tr class="hover:bg-gista-50 transition {{ $loop->even ? 'bg-gista-50/50' : '' }}">
                            <td class="py-3 px-4 text-center text-slate-400">{{ $documents->firstItem() + $idx }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-gista-600">
                                <a href="{{ route('payment.show', $doc->id) }}" class="hover:underline">
                                    {{ $doc->optg_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border 
                                    {{ $doc->status === 'Approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $doc->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $doc->optg_number }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $doc->approved_at ? $doc->approved_at->format('d/m/Y') : ($doc->created_at->format('d/m/Y')) }}</td>
                            <td class="py-3 px-4 text-center font-mono font-semibold {{ $doc->due_date?->isPast() ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $doc->due_date?->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono">{{ $doc->currency }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">
                                {{ number_format($doc->total_payment, 2, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button wire:click="viewStatus({{ $doc->id }})" class="px-3 py-1 rounded-lg text-xs font-bold transition shadow-sm
                                    {{ $doc->payment_status === 'Paid' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 
                                       ($doc->payment_status === 'Payment Failed' ? 'bg-rose-500 text-white hover:bg-rose-600' : 'bg-amber-500 text-white hover:bg-amber-600') }}">
                                    {{ $doc->payment_status }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                Tidak ada data report jatuh tempo ditemukan.
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

    <!-- POP UP STATUS BAYAR (PDF Page 40) -->
    @if($showStatusModal && $selectedDoc)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3">
                    <div>
                        <span class="text-xs font-mono font-bold text-gista-600">Status Bayar Dokumen</span>
                        <h3 class="text-base font-bold text-slate-800 font-mono">{{ $selectedDoc->optg_number }}</h3>
                    </div>
                    <button wire:click="$set('showStatusModal', false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Bayar Keseluruhan:</span>
                            <span class="font-bold {{ $selectedDoc->payment_status === 'Paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $selectedDoc->payment_status }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Vendor:</span>
                            <span class="font-bold text-slate-700">{{ $selectedDoc->vendor_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tanggal Bayar:</span>
                            <span class="font-mono text-slate-700">{{ $selectedDoc->paid_at ? $selectedDoc->paid_at->format('d/m/Y H:i:s') : '-' }}</span>
                        </div>
                    </div>

                    <!-- DPP Breakdown -->
                    <div class="p-3 border border-slate-200 rounded-xl space-y-1.5">
                        <div class="flex justify-between font-semibold">
                            <span>Pembayaran DPP</span>
                            <span class="font-mono text-gista-700">IDR {{ number_format($selectedDoc->total_dpp, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[11px] text-slate-500">
                            <span>Rekening DPP:</span>
                            <span class="font-mono">{{ $selectedDoc->dpp_account_number }} ({{ $selectedDoc->dpp_account_name }})</span>
                        </div>
                    </div>

                    <!-- PPN Breakdown -->
                    <div class="p-3 border border-slate-200 rounded-xl space-y-1.5">
                        <div class="flex justify-between font-semibold">
                            <span>Pembayaran PPN</span>
                            <span class="font-mono text-gista-700">IDR {{ number_format($selectedDoc->total_ppn, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[11px] text-slate-500">
                            <span>Rekening PPN:</span>
                            <span class="font-mono">{{ $selectedDoc->ppn_account_number }} ({{ $selectedDoc->ppn_account_name }})</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gista-100">
                    <button wire:click="$set('showStatusModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">
                        Tutup
                    </button>

                    @if($selectedDoc->payment_status === 'Paid')
                        <a href="{{ route('payment.bukti-bayar', $selectedDoc->id) }}" target="_blank" class="px-4 py-2 bg-gista-600 hover:bg-gista-700 text-white text-xs font-bold rounded-xl flex items-center space-x-1 shadow-sm transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Bukti Bayar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
