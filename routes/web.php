<?php

use App\Livewire\Gista\BuktiBayar;
use App\Livewire\Gista\CreatePayment;
use App\Livewire\Gista\DashboardPayment;
use App\Livewire\Gista\Inbox;
use App\Livewire\Gista\PaymentDisplay;
use App\Livewire\Gista\PaymentList;
use App\Livewire\Gista\ReportJatuhTempo;
use App\Livewire\Gista\SelectPaymentType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard/payment or login
Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware(['auth'])->group(function () {
    // 1. Payment List & Management (Dashboard is Payment List)
    Route::get('/dashboard', PaymentList::class)->name('dashboard');
    Route::get('/payment', PaymentList::class)->name('payment.index');
    Route::get('/payment/select-type', SelectPaymentType::class)->name('payment.select-type');
    Route::get('/payment/create', CreatePayment::class)->name('payment.create');
    Route::get('/payment/{document}', PaymentDisplay::class)->name('payment.show');
    Route::get('/payment/{document}/bukti-bayar', BuktiBayar::class)->name('payment.bukti-bayar');

    // 2. Profile
    Route::view('profile', 'profile')->name('profile');

    // 2. Inbox Approval (Checker / SPV 1)
    Route::get('/inbox', Inbox::class)->name('inbox');

    // 3. Dashboard Payment (SPV 2 - Biro Payment)
    Route::get('/dashboard-payment', DashboardPayment::class)->name('dashboard.payment');

    // 4. Report Jatuh Tempo
    Route::get('/report-jatuh-tempo', ReportJatuhTempo::class)->name('report.jatuh-tempo');

    // Quick demo role switcher
    Route::post('/demo/switch-role/{role}', function (string $role) {
        $user = User::where('role', $role)->first();
        if ($user) {
            Auth::login($user);
        }
        return back();
    })->name('demo.switch-role');

    // Logout
    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

require __DIR__.'/auth.php';
