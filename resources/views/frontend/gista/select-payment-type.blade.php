<div class="max-w-4xl mx-auto py-8">
    <!-- Back Button & Title (PDF Page 7) -->
    <div class="flex items-center space-x-3 mb-6">
        <a href="{{ route('payment.index') }}" class="inline-flex items-center p-2 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-600 hover:bg-gista-50 active:scale-[0.98] transition-all duration-150">
            <x-icon name="arrow-left" class="w-4 h-4" />
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">Jenis Pembayaran</h2>
            <p class="text-xs text-slate-500">Pilih jenis pembayaran yang Anda inginkan</p>
        </div>
    </div>

    <!-- 3 Payment Option Cards (PDF Page 7) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
        <!-- Option 1: PO Register -->
        <div class="bg-white rounded-2xl border border-gista-200 p-8 flex flex-col items-center text-center opacity-60 hover:opacity-100 transition cursor-not-allowed">
            <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-slate-500 mb-6 shadow-sm">
                <x-icon name="clipboard" class="w-8 h-8" />
            </div>
            <h3 class="font-bold text-slate-700 text-sm mb-1">Pembayaran PO Register</h3>
            <p class="text-[11px] text-slate-500">Pembayaran pengadaan barang terdaftar</p>
        </div>

        <!-- Option 2: PO Standar (Active / Recommended) -->
        <a href="{{ route('payment.create') }}" class="group bg-white rounded-2xl border-2 border-gista-600 p-8 flex flex-col items-center text-center shadow-sm hover:shadow-md hover:border-gista-700 active:scale-[0.99] transition-all duration-150 relative">
            <span class="absolute -top-3 px-3 py-0.5 rounded-full bg-gista-700 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                Rekomendasi
            </span>
            <div class="w-16 h-16 rounded-2xl bg-gista-50 border border-gista-100 flex items-center justify-center text-gista-700 mb-6 shadow-sm group-hover:bg-gista-600 group-hover:text-white transition-colors duration-150">
                <x-icon name="document-text" class="w-8 h-8" />
            </div>
            <h3 class="font-bold text-slate-800 text-base mb-1">Pembayaran PO Standar</h3>
            <p class="text-[11px] text-slate-500 mb-5 leading-relaxed">Pengajuan tagihan pengadaan barang & jasa berbasis PO Standard (POST)</p>
            <span class="inline-flex items-center px-4 py-2 bg-gista-600 group-hover:bg-gista-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-colors duration-150">
                Pilih Jenis Ini &rarr;
            </span>
        </a>

        <!-- Option 3: PO Sewa -->
        <div class="bg-white rounded-2xl border border-gista-200 p-8 flex flex-col items-center text-center opacity-60 hover:opacity-100 transition cursor-not-allowed">
            <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-slate-500 mb-6 shadow-sm">
                <x-icon name="building" class="w-8 h-8" />
            </div>
            <h3 class="font-bold text-slate-700 text-sm mb-1">Pembayaran PO Sewa</h3>
            <p class="text-[11px] text-slate-500">Pembayaran termin sewa gedung & alat</p>
        </div>
    </div>
</div>
