<div class="max-w-5xl mx-auto space-y-6 pb-16">
    <!-- Header (PDF Page 33) -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center space-x-2">
                <svg class="w-6 h-6 text-gista-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Inbox Persetujuan Pembayaran</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</p>
        </div>
        <div class="w-72">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nomor OPTG, vendor, deskripsi..." class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-gista-500 outline-none">
        </div>
    </div>

    <!-- Permohonan List Cards (PDF Page 33) -->
    <div class="space-y-4">
        @forelse($items as $doc)
            <a href="{{ route('payment.show', $doc->id) }}" class="block bg-white rounded-2xl border border-gista-200 shadow-sm p-5 hover:border-gista-400 hover:shadow-md transition">
                <div class="flex items-center justify-between border-b border-gista-100 pb-3 mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gista-500"></span>
                        <h3 class="text-sm font-bold text-slate-800">
                            Permohonan Persetujuan Pembayaran Tagihan &mdash; <span class="font-mono text-gista-600">{{ $doc->optg_number }}</span>
                        </h3>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-[10px] font-bold">BARU</span>
                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 text-[10px] font-bold">INTRANET</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            {{ $doc->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400">Tanggal Pengajuan:</span>
                        <p class="font-medium text-slate-700 mt-0.5">{{ $doc->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Diajukan Oleh:</span>
                        <p class="font-semibold text-slate-800 mt-0.5 truncate">{{ $doc->creator?->name }} ({{ $doc->creator?->nip }})</p>
                    </div>
                    <div>
                        <span class="text-slate-400">No. Invoice Vendor:</span>
                        <p class="font-mono text-slate-700 mt-0.5">{{ $doc->vendor_invoice_number ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Vendor:</span>
                        <p class="font-bold text-slate-800 mt-0.5 truncate">{{ $doc->vendor_name }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-slate-400">Keterangan Pembayaran:</span>
                        <p class="text-slate-700 mt-0.5">{{ $doc->description }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold">Nilai Total Tagihan:</span>
                        <p class="font-mono font-extrabold text-gista-700 text-sm mt-0.5">IDR {{ number_format($doc->total_payment, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Tanggal Perlambatan:</span>
                        <p class="font-mono text-slate-700 mt-0.5">{{ $doc->requested_due_date ? $doc->requested_due_date->format('d/m/Y') : '-' }}</p>
                    </div>

                    @if($doc->delay_reason)
                        <div class="md:col-span-4 bg-amber-50/50 p-2.5 rounded-xl border border-amber-100 flex items-center justify-between">
                            <span class="text-amber-800 font-semibold">Alasan Perlambatan: {{ $doc->delay_reason }}</span>
                            <span class="text-gista-600 font-bold hover:underline">Klik untuk Review &amp; Aksi &rarr;</span>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl border border-gista-200 p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-sm font-semibold">Tidak ada permohonan persetujuan yang menunggu.</p>
                <p class="text-xs text-slate-400 mt-1">Semua dokumen tagihan pembayaran telah diproses.</p>
            </div>
        @endforelse
    </div>
</div>
