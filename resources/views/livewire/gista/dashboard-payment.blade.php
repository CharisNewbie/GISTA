<div class="max-w-7xl mx-auto space-y-8 pb-16">
    <!-- Title -->
    <div>
        <h2 class="text-xl font-bold text-slate-800">Dashboard Payment &mdash; Biro Pengolahan Pembayaran</h2>
        <p class="text-xs text-slate-500 mt-0.5">Monitoring jadwal jatuh tempo pembayaran eksternal dan verifikasi akhir (Approval 2)</p>
    </div>

    <!-- Metrics Cards (PDF Page 35) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-800">Pembayaran Eksternal &bull; Status Jatuh Tempo</h3>
            <div class="flex items-center space-x-3 text-[11px] font-medium text-slate-500">
                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-purple-500 mr-1.5"></span> Waiting For Approval</span>
                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-1.5"></span> Pending</span>
                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-cyan-500 mr-1.5"></span> Waiting For Cancellation</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Jatuh Tempo Hari Ini -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-700">Jatuh Tempo Hari Ini</p>
                        <p class="text-[10px] text-slate-400">{{ now()->format('d F Y') }}</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500">Total Transaksi: <strong class="text-slate-800 text-sm font-mono">{{ $todayCount }}</strong></span>
                </div>
                <div class="space-y-1.5 pt-2">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-purple-500 mr-1.5"></span> Approval</span>
                        <span class="font-mono font-bold">{{ $todayCount }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></span> Pending</span>
                        <span class="font-mono font-bold">0</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-cyan-500 mr-1.5"></span> Cancellation</span>
                        <span class="font-mono font-bold">0</span>
                    </div>
                </div>
            </div>

            <!-- Jatuh Tempo H+1 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-700">Jatuh Tempo H+1</p>
                        <p class="text-[10px] text-slate-400">{{ now()->addDay()->format('d F Y') }}</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500">Total Transaksi: <strong class="text-slate-800 text-sm font-mono">{{ $h1Count }}</strong></span>
                </div>
                <div class="space-y-1.5 pt-2">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-purple-500 mr-1.5"></span> Approval</span>
                        <span class="font-mono font-bold">{{ $h1Count }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></span> Pending</span>
                        <span class="font-mono font-bold">0</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-cyan-500 mr-1.5"></span> Cancellation</span>
                        <span class="font-mono font-bold">0</span>
                    </div>
                </div>
            </div>

            <!-- Jatuh Tempo H+2 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-700">Jatuh Tempo H+2</p>
                        <p class="text-[10px] text-slate-400">{{ now()->addDays(2)->format('d F Y') }}</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500">Total Transaksi: <strong class="text-slate-800 text-sm font-mono">{{ $h2Count }}</strong></span>
                </div>
                <div class="space-y-1.5 pt-2">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-purple-500 mr-1.5"></span> Approval</span>
                        <span class="font-mono font-bold">{{ $h2Count }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></span> Pending</span>
                        <span class="font-mono font-bold">0</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-cyan-500 mr-1.5"></span> Cancellation</span>
                        <span class="font-mono font-bold">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Approval Section (PDF Page 35-37) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Outstanding Approval (Waiting for Approval 2)</h3>
                <p class="text-xs text-slate-400">Daftar tagihan yang menunggu persetujuan tim Biro Payment</p>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full">
                {{ $outstanding->count() }} Dokumen Menunggu
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No. Dokumen</th>
                        <th class="py-3 px-4">No. PO Referensi</th>
                        <th class="py-3 px-4">Vendor</th>
                        <th class="py-3 px-4 text-right">Nilai Total Tagihan</th>
                        <th class="py-3 px-4 text-center">Jatuh Tempo</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($outstanding as $doc)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-cyan-600">{{ $doc->optg_number }}</td>
                            <td class="py-3 px-4 font-mono">{{ $doc->po_number }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $doc->vendor_name }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">IDR {{ number_format($doc->total_payment, 2, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center font-mono text-slate-600">{{ $doc->due_date?->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('payment.show', $doc->id) }}" class="px-4 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-lg shadow-sm transition">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Tidak ada dokumen outstanding menunggu persetujuan Approval 2.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
