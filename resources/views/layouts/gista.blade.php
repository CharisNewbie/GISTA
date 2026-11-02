<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GISTA Payment') }} - BCA Logistic Assistant</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen text-slate-800 print:bg-white">
    <div class="flex h-screen overflow-hidden print:h-auto print:overflow-visible">
        <!-- BCA Logistic Assistant Leftmost Sidebar Icon Bar -->
        <aside class="print:hidden w-16 bg-white border-r border-slate-200 flex flex-col items-center py-4 justify-between z-30 shrink-0">
            <div class="flex flex-col items-center space-y-6">
                <!-- App Logo / Headphone Icon -->
                <a href="{{ route('payment.index') }}" class="w-10 h-10 rounded-xl bg-cyan-600 flex items-center justify-center text-white shadow-md shadow-cyan-200 hover:bg-cyan-700 transition" title="GISTA Logistic Assistant">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                </a>

                <!-- User Role Initial Badge -->
                <div class="w-10 h-10 rounded-full bg-cyan-100 text-cyan-800 font-bold flex items-center justify-center text-sm shadow-inner" title="{{ auth()->user()?->name ?? 'User' }} ({{ strtoupper(auth()->user()?->role ?? 'GUEST') }})">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'US', 0, 2)) }}
                </div>

                <!-- Navigation Icons -->
                <nav class="flex flex-col items-center space-y-4">
                    <!-- Home / Payments -->
                    <a href="{{ route('payment.index') }}" class="p-2.5 rounded-xl transition {{ request()->routeIs('payment.*') ? 'bg-cyan-50 text-cyan-600' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}" title="Pembayaran Eksternal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </a>

                    <!-- Inbox / Approvals -->
                    <a href="{{ route('inbox') }}" class="relative p-2.5 rounded-xl transition {{ request()->routeIs('inbox') ? 'bg-cyan-50 text-cyan-600' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}" title="Inbox Persetujuan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        @php
                            $pendingCount = \App\Models\PaymentDocument::whereIn('status', ['Waiting for Approval 1', 'Waiting for Approval 2', 'Waiting for Checker'])->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-cyan-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Dashboard Payment (SPV 2 / Analytics) -->
                    <a href="{{ route('dashboard.payment') }}" class="p-2.5 rounded-xl transition {{ request()->routeIs('dashboard.payment') ? 'bg-cyan-50 text-cyan-600' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}" title="Dashboard Payment">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </a>

                    <!-- Report Jatuh Tempo -->
                    <a href="{{ route('report.jatuh-tempo') }}" class="p-2.5 rounded-xl transition {{ request()->routeIs('report.jatuh-tempo') ? 'bg-cyan-50 text-cyan-600' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}" title="Report Jatuh Tempo">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </a>
                </nav>
            </div>

            <!-- Bottom Role Switcher & Logout -->
            <div class="flex flex-col items-center space-y-3">
                <!-- Role Quick Switch dropdown for demo -->
                <div class="relative group">
                    <button class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-semibold" title="Ganti Role Cepat">
                        🎭
                    </button>
                    <div class="absolute left-12 bottom-0 w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-2 hidden group-hover:block z-50">
                        <p class="text-[11px] font-bold text-slate-400 uppercase px-2 py-1">Demo Quick Switch</p>
                        <form method="POST" action="{{ route('demo.switch-role', 'operator') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-slate-100 flex items-center justify-between {{ auth()->user()?->role === 'operator' ? 'font-bold text-cyan-600' : '' }}">
                                Operator (Input)
                            </button>
                        </form>
                        <form method="POST" action="{{ route('demo.switch-role', 'checker') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-slate-100 flex items-center justify-between {{ auth()->user()?->role === 'checker' ? 'font-bold text-cyan-600' : '' }}">
                                Checker (Verifikasi)
                            </button>
                        </form>
                        <form method="POST" action="{{ route('demo.switch-role', 'spv1') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-slate-100 flex items-center justify-between {{ auth()->user()?->role === 'spv1' ? 'font-bold text-cyan-600' : '' }}">
                                Supervisor 1 (Unit)
                            </button>
                        </form>
                        <form method="POST" action="{{ route('demo.switch-role', 'spv2') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-slate-100 flex items-center justify-between {{ auth()->user()?->role === 'spv2' ? 'font-bold text-cyan-600' : '' }}">
                                Supervisor 2 (Biro)
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2.5 rounded-xl text-rose-500 hover:bg-rose-50 transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Secondary Navigation Sidebar for Sub-Menus -->
        <aside class="print:hidden w-64 bg-white border-r border-slate-200 flex flex-col shrink-0">
            <!-- App Header -->
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <span class="text-cyan-600 font-extrabold text-xl tracking-tight">GISTA</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-cyan-100 text-cyan-800">Payment</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Pembayaran Eksternal V.1.0</p>
                <div class="mt-3 pt-3 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-700 truncate">{{ auth()->user()?->name ?? 'User' }}</p>
                    <p class="text-[11px] text-cyan-600 font-medium truncate">{{ auth()->user()?->unit_kerja ?? 'Internal BCA' }}</p>
                    <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono">
                        {{ strtoupper(auth()->user()?->role ?? 'GUEST') }} • NIP: {{ auth()->user()?->nip ?? '-' }}
                    </span>
                </div>
            </div>

            <!-- Sub Navigation Items (PDF Page 6) -->
            <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
                <a href="{{ route('payment.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('payment.index') ? 'bg-cyan-50 text-cyan-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span class="w-2.5 h-2.5 rounded-full mr-3 {{ request()->routeIs('payment.index') ? 'bg-cyan-500' : 'bg-slate-300' }}"></span>
                    List Pembayaran Tagihan
                </a>

                <a href="{{ route('payment.select-type') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('payment.select-type') || request()->routeIs('payment.create') ? 'bg-cyan-50 text-cyan-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span class="w-2.5 h-2.5 rounded-full mr-3 {{ request()->routeIs('payment.select-type') || request()->routeIs('payment.create') ? 'bg-cyan-500' : 'bg-slate-300' }}"></span>
                    Buat Pembayaran Tagihan
                </a>

                <a href="{{ route('payment.index', ['filter' => 'incomplete']) }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-xl transition text-slate-600 hover:bg-slate-50">
                    <span class="w-2.5 h-2.5 rounded-full mr-3 bg-slate-300"></span>
                    List Dokumen Incomplete
                </a>

                <a href="{{ route('report.jatuh-tempo') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-xl transition {{ request()->routeIs('report.jatuh-tempo') ? 'bg-cyan-50 text-cyan-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span class="w-2.5 h-2.5 rounded-full mr-3 {{ request()->routeIs('report.jatuh-tempo') ? 'bg-cyan-500' : 'bg-slate-300' }}"></span>
                    Report Jatuh Tempo
                </a>

                <div class="pt-4 mt-4 border-t border-slate-100">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Approval & Monitor</p>

                    <a href="{{ route('inbox') }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium rounded-xl transition {{ request()->routeIs('inbox') ? 'bg-cyan-50 text-cyan-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full mr-3 {{ request()->routeIs('inbox') ? 'bg-cyan-500' : 'bg-amber-400' }}"></span>
                            Inbox Persetujuan
                        </span>
                        @if($pendingCount > 0)
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('dashboard.payment') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-xl transition {{ request()->routeIs('dashboard.payment') ? 'bg-cyan-50 text-cyan-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="w-2.5 h-2.5 rounded-full mr-3 {{ request()->routeIs('dashboard.payment') ? 'bg-cyan-500' : 'bg-slate-300' }}"></span>
                        Dashboard Payment
                    </a>
                </div>
            </nav>

            <div class="p-3 border-t border-slate-100 text-center">
                <span class="text-[10px] text-slate-400 font-medium">BCA Internal Systems &copy; 2026</span>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-slate-50 print:bg-white print:overflow-visible">
            <!-- Top Greeting Banner matching PDF Page 5 -->
            <header class="print:hidden bg-gradient-to-r from-cyan-500 via-teal-500 to-cyan-600 text-white px-8 py-5 shadow-sm shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold flex items-center space-x-2">
                            <span>Selamat {{ now()->hour < 12 ? 'pagi' : (now()->hour < 15 ? 'siang' : (now()->hour < 18 ? 'sore' : 'malam')) }}, {{ auth()->user()?->name ?? 'User' }}!</span>
                        </h1>
                        <p class="text-cyan-100 text-xs mt-0.5">Selamat datang di aplikasi LOGISTIC ASSISTANT - GISTA Payment</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/20 font-medium">
                            Cabang: {{ auth()->user()?->kode_cabang ?? '0971' }}
                        </span>
                        <span class="text-xs bg-white text-cyan-800 font-bold px-3 py-1.5 rounded-lg shadow-sm">
                            {{ date('d M Y') }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Body Slot -->
            <div class="p-6 flex-1 print:p-0">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
