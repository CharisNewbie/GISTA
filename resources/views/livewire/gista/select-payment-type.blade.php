<div class="max-w-4xl mx-auto py-8">
    <!-- Back Button & Title (PDF Page 7) -->
    <div class="flex items-center space-x-3 mb-6">
        <a href="{{ route('payment.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">Jenis Pembayaran</h2>
            <p class="text-xs text-slate-500">Pilih jenis pembayaran yang Anda inginkan</p>
        </div>
    </div>

    <!-- 3 Payment Option Cards (PDF Page 7) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
        <!-- Option 1: PO Register -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 flex flex-col items-center text-center opacity-60 hover:opacity-100 transition cursor-not-allowed">
            <div class="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-cyan-600 mb-6 shadow-sm">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-700 text-sm mb-1">Pembayaran PO Register</h3>
            <p class="text-[11px] text-slate-400">Pembayaran pengadaan barang terdaftar</p>
        </div>

        <!-- Option 2: PO Standar (Active / Recommended) -->
        <a href="{{ route('payment.create') }}" class="group bg-white rounded-2xl border-2 border-cyan-500 p-8 flex flex-col items-center text-center shadow-lg shadow-cyan-100 hover:scale-105 transition transform relative">
            <span class="absolute -top-3 px-3 py-0.5 rounded-full bg-cyan-600 text-white text-[10px] font-bold uppercase tracking-wider">
                Rekomendasi
            </span>
            <div class="w-20 h-20 rounded-2xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600 mb-6 shadow-sm group-hover:bg-cyan-600 group-hover:text-white transition">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-cyan-800 text-base mb-1">Pembayaran PO Standar</h3>
            <p class="text-[11px] text-slate-500 mb-4">Pengajuan tagihan pengadaan barang & jasa berbasis PO Standard (POST)</p>
            <span class="inline-flex items-center text-xs font-semibold text-cyan-600 group-hover:text-cyan-800">
                Pilih Jenis Ini
                <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </a>

        <!-- Option 3: PO Sewa -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 flex flex-col items-center text-center opacity-60 hover:opacity-100 transition cursor-not-allowed">
            <div class="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-cyan-600 mb-6 shadow-sm">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-700 text-sm mb-1">Pembayaran PO Sewa</h3>
            <p class="text-[11px] text-slate-400">Pembayaran termin sewa gedung & alat</p>
        </div>
    </div>
</div>
