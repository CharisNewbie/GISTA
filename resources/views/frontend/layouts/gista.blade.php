<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0060af">
    <title>{{ config('app.name', 'GISTA Payment') }} - BCA Logistic Assistant</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gista-50 min-h-screen text-slate-800 print:bg-white" x-data="{ mobileNav: false }">
    <!-- Global Livewire Loading Progress Bar -->
    <div wire:loading.delay class="fixed top-0 inset-x-0 h-1 bg-gradient-to-r from-gista-400 via-gista-400 to-gista-600 z-[9999] shadow-md animate-pulse"></div>

    <!-- Unified Toast Notifications: session flashes on load + Livewire 'gista-toast' browser events in-app -->
    @php
        $__toasts = [];
        if (session()->has('success')) {
            $__toasts[] = ['id' => 'sess-success', 'type' => 'success', 'title' => 'Berhasil', 'message' => session('success'), 'timeout' => 4500];
        }
        if (session()->has('error')) {
            $__toasts[] = ['id' => 'sess-error', 'type' => 'error', 'title' => 'Akses Ditolak', 'message' => session('error'), 'timeout' => 5000];
        }
    @endphp
    <div x-data="{
        toasts: @js($__toasts),
        addToast(detail) {
            const toast = { id: 'lw-' + Date.now() + '-' + Math.floor(Math.random() * 10000), show: true, timeout: detail.timeout ?? 4500, ...detail };
            this.toasts.push(toast);
        },
        dismiss(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (!toast) return;
            toast.show = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 200);
        }
    }" x-on:gista-toast.window="addToast($event.detail)"
        class="fixed top-4 right-4 z-[100] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show"
                 x-init="setTimeout(() => dismiss(toast.id), toast.timeout)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 :class="toast.type === 'error' ? 'border-rose-200 bg-white ring-rose-500/10' : 'border-emerald-200 bg-white ring-emerald-500/10'"
                 class="pointer-events-auto bg-white border rounded-xl shadow-lg ring-1 ring-slate-900/5 p-4 flex items-start space-x-3 transition-all">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                     :class="toast.type === 'error' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <template x-if="toast.type === 'error'">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </template>
                        <template x-if="toast.type !== 'error'">
                            <path d="M5 13l4 4L19 7" />
                        </template>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-1.5">
                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded"
                              :class="toast.type === 'error' ? 'bg-rose-100/70 text-rose-700' : 'bg-emerald-100/70 text-emerald-700'"
                              x-text="toast.title"></span>
                    </div>
                    <p class="text-xs text-slate-700 mt-1 leading-relaxed" x-text="toast.message"></p>
                </div>
                <button @click="dismiss(toast.id)" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition active:scale-95" title="Tutup">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @php
        $authUser = auth()->user();
        $canCreatePayment = $authUser?->isOperator() ?? false;
        $canApprove = ($authUser?->isChecker() ?? false) || ($authUser?->isSpv1() ?? false);
        $canViewReport = ($authUser?->isSpv1() ?? false) || ($authUser?->isSpv2() ?? false);
        $canViewDashboard = $authUser?->isSpv2() ?? false;
        $pendingCount = $canApprove
            ? \App\Backend\Models\PaymentDocument::whereIn('status', ['Waiting for Approval 1', 'Waiting for Approval 2', 'Waiting for Checker'])->count()
            : 0;
    @endphp

    <div class="flex h-screen overflow-hidden print:h-auto print:overflow-visible">
        <!-- Secondary Navigation Sidebar for Sub-Menus -->
        <aside class="print:hidden bg-white border-r border-gista-200 flex flex-col shrink-0 transition-all duration-300 md:w-64" :class="mobileNav ? 'fixed inset-y-0 left-0 z-40 w-64 shadow-2xl' : 'hidden md:flex w-64'">
            <!-- App Header -->
            <div class="p-5 border-b border-gista-100">
                <div class="flex items-center space-x-2">
                    <span class="text-gista-600 font-extrabold text-xl tracking-tight">GISTA</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-gista-100 text-gista-800">Payment</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Pembayaran Eksternal V.1.0</p>
                <div class="mt-3 pt-3 border-t border-gista-100">
                    <p class="text-xs font-bold text-slate-700 truncate">{{ auth()->user()?->name ?? 'User' }}</p>
                    <p class="text-[11px] text-gista-600 font-medium truncate">{{ auth()->user()?->unit_kerja ?? 'Internal BCA' }}</p>
                    <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono">
                        {{ strtoupper(auth()->user()?->role ?? 'GUEST') }} • NIP: {{ auth()->user()?->nip ?? '-' }}
                    </span>
                </div>
            </div>

            <!-- Sub Navigation Items (PDF Page 6) — tampil sesuai akses role -->
            <nav class="flex-1 p-3 space-y-1.5 overflow-y-auto">
                <a href="{{ route('payment.index') }}" class="group flex items-center px-3 py-2.5 text-xs rounded-xl transition {{ request()->routeIs('payment.index') && request('filter') !== 'incomplete' ? 'bg-gista-50 text-gista-800 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 shrink-0 {{ request()->routeIs('payment.index') && request('filter') !== 'incomplete' ? 'bg-gista-500 text-white' : 'bg-gista-100 text-gista-700' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </span>
                    List Pembayaran Tagihan
                </a>

                @if($canCreatePayment)
                    <a href="{{ route('payment.select-type') }}" class="group flex items-center px-3 py-2.5 text-xs rounded-xl transition {{ request()->routeIs('payment.select-type') || request()->routeIs('payment.create') ? 'bg-gista-50 text-gista-800 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 shrink-0 {{ request()->routeIs('payment.select-type') || request()->routeIs('payment.create') ? 'bg-gista-500 text-white' : 'bg-gista-100 text-gista-600' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </span>
                        Buat Pembayaran Tagihan
                    </a>

                    <a href="{{ route('payment.index', ['filter' => 'incomplete']) }}" class="group flex items-center px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('payment.index') && request('filter') === 'incomplete' ? 'bg-gista-50 text-gista-800 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 shrink-0 {{ request()->routeIs('payment.index') && request('filter') === 'incomplete' ? 'bg-gista-500 text-white' : 'bg-slate-100 text-slate-500' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3" /></svg>
                        </span>
                        List Dokumen Incomplete
                    </a>
                @endif

                @if($canViewReport)
                    <a href="{{ route('report.jatuh-tempo') }}" class="group flex items-center px-3 py-2.5 text-xs rounded-xl transition {{ request()->routeIs('report.jatuh-tempo') ? 'bg-gista-50 text-gista-800 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 shrink-0 {{ request()->routeIs('report.jatuh-tempo') ? 'bg-gista-500 text-white' : 'bg-gista-100 text-gista-700' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </span>
                        Report Pembayaran Tagihan
                    </a>
                @endif

                @if($canApprove || $canViewDashboard)
                    <div class="pt-4 mt-4 border-t border-gista-100">
                        <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Approval &amp; Monitor</p>

                        @if($canApprove)
                            <a href="{{ route('inbox') }}" class="group flex items-center justify-between px-3 py-2.5 text-xs rounded-xl transition {{ request()->routeIs('inbox') ? 'bg-gista-50 text-gista-800 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                                <span class="flex items-center">
                                    <span class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 shrink-0 {{ request()->routeIs('inbox') ? 'bg-gista-500 text-white' : 'bg-amber-100 text-amber-600' }}">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </span>
                                    Inbox Persetujuan
                                </span>
                                @if($pendingCount > 0)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        @endif

                        @if($canViewDashboard)
                            <a href="{{ route('dashboard.payment') }}" class="group flex items-center px-3 py-2.5 text-xs rounded-xl transition {{ request()->routeIs('dashboard.payment') ? 'bg-gista-50 text-gista-800 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                                <span class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 shrink-0 {{ request()->routeIs('dashboard.payment') ? 'bg-gista-500 text-white' : 'bg-gista-100 text-gista-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                </span>
                                Dashboard Payment
                            </a>
                        @endif
                    </div>
                @endif
            </nav>

            <!-- Footer: demo role switcher + logout -->
            <div class="border-t border-gista-100 p-3 space-y-2">
                @if(app()->environment('local'))
                    <details class="group">
                        <summary class="list-none cursor-pointer select-none flex items-center justify-between px-2 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition">
                            <span>Demo Ganti Role</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="mt-1.5 grid grid-cols-2 gap-1.5">
                            <form method="POST" action="{{ route('demo.switch-role', 'operator') }}">
                                @csrf
                                <button type="submit" class="w-full px-2 py-1.5 text-[10px] rounded-lg border text-left transition {{ auth()->user()?->isOperator() ? 'bg-gista-50 border-gista-300 text-gista-700 font-bold' : 'bg-white border-slate-200 text-slate-500 hover:bg-gista-50' }}">Operator</button>
                            </form>
                            <form method="POST" action="{{ route('demo.switch-role', 'checker') }}">
                                @csrf
                                <button type="submit" class="w-full px-2 py-1.5 text-[10px] rounded-lg border text-left transition {{ auth()->user()?->isChecker() ? 'bg-gista-50 border-gista-300 text-gista-700 font-bold' : 'bg-white border-slate-200 text-slate-500 hover:bg-gista-50' }}">Checker</button>
                            </form>
                            <form method="POST" action="{{ route('demo.switch-role', 'spv1') }}">
                                @csrf
                                <button type="submit" class="w-full px-2 py-1.5 text-[10px] rounded-lg border text-left transition {{ auth()->user()?->isSpv1() ? 'bg-gista-50 border-gista-300 text-gista-700 font-bold' : 'bg-white border-slate-200 text-slate-500 hover:bg-gista-50' }}">SPV 1</button>
                            </form>
                            <form method="POST" action="{{ route('demo.switch-role', 'spv2') }}">
                                @csrf
                                <button type="submit" class="w-full px-2 py-1.5 text-[10px] rounded-lg border text-left transition {{ auth()->user()?->isSpv2() ? 'bg-gista-50 border-gista-300 text-gista-700 font-bold' : 'bg-white border-slate-200 text-slate-500 hover:bg-gista-50' }}">SPV 2</button>
                            </form>
                        </div>
                    </details>
                @endif

                <div class="flex items-center justify-between pt-1">
                    <span class="text-[10px] text-slate-400 font-medium">BCA Internal Systems &copy; 2026</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 active:scale-95 flex items-center justify-center shadow-sm shadow-rose-500/30 transition" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-gista-50/60 print:bg-white print:overflow-visible">
            <!-- Top Greeting Bar: flat, no gradient, no glass -->
            <header class="print:hidden bg-white border-b border-slate-200 px-6 md:px-8 py-4 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button @click="mobileNav = !mobileNav" class="md:hidden mr-3 p-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition focus:outline-none" title="Menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-lg md:text-xl font-bold text-slate-900 flex items-center space-x-2">
                                <span>Selamat {{ now()->hour < 12 ? 'pagi' : (now()->hour < 15 ? 'siang' : (now()->hour < 18 ? 'sore' : 'malam')) }}, {{ auth()->user()?->name ?? 'User' }}!</span>
                            </h1>
                            <p class="text-slate-500 text-xs mt-0.5">GISTA Payment &bull; Pembayaran Eksternal</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs border border-slate-200 bg-white px-3 py-1 rounded-lg text-slate-600 font-medium">
                            Cabang: {{ auth()->user()?->kode_cabang ?? '0971' }}
                        </span>
                        <span class="text-xs border border-slate-200 bg-white px-3 py-1 rounded-lg text-slate-700 font-bold">
                            {{ date('d M Y') }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Body Slot -->
            <div class="p-4 flex-1 print:p-0">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
