<?php

/*
|--------------------------------------------------------------------------
| Backend Routes — GISTA Payment
|--------------------------------------------------------------------------
| Semua route bisnis/fitur GISTA (Livewire full-page components, download
| lampiran, inbox approval, dashboard, report) dipisahkan dari route umum
| di routes/web.php agar struktur frontend/backend lebih rapi.
*/

use App\Backend\Http\Controllers\AttachmentDownloadController;
use App\Backend\Livewire\Gista\BuktiBayar;
use App\Backend\Livewire\Gista\CreatePayment;
use App\Backend\Livewire\Gista\DashboardPayment;
use App\Backend\Livewire\Gista\Inbox;
use App\Backend\Livewire\Gista\PaymentDisplay;
use App\Backend\Livewire\Gista\PaymentList;
use App\Backend\Livewire\Gista\ReportJatuhTempo;
use App\Backend\Livewire\Gista\SelectPaymentType;
use App\Backend\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // 1. Payment List & Management (Dashboard is Payment List)
    Route::get('/dashboard', PaymentList::class)->name('dashboard')->middleware('check.role:operator,checker,spv1,spv2');
    Route::get('/payment', PaymentList::class)->name('payment.index')->middleware('check.role:operator,checker,spv1,spv2');
    Route::get('/payment/select-type', SelectPaymentType::class)->name('payment.select-type')->middleware('check.role:operator');
    Route::get('/payment/create', CreatePayment::class)->name('payment.create')->middleware('check.role:operator');
    Route::get('/payment/{document}', PaymentDisplay::class)->name('payment.show')->middleware('check.role:operator,checker,spv1,spv2');
    Route::get('/payment/{document}/bukti-bayar', BuktiBayar::class)->name('payment.bukti-bayar')->middleware('check.role:operator,checker,spv1,spv2');

    // 2. Secure attachment download (private storage, authorization-checked)
    Route::get('/attachments/{attachment}', AttachmentDownloadController::class)
        ->name('attachments.download')
        ->middleware('check.role:operator,checker,spv1,spv2');

    // 3. Inbox Approval (Checker / SPV 1)
    Route::get('/inbox', Inbox::class)->name('inbox')->middleware('check.role:checker,spv1');

    // 4. Dashboard Payment (SPV 2 - Biro Payment)
    Route::get('/dashboard-payment', DashboardPayment::class)->name('dashboard.payment')->middleware('check.role:spv2');

    // 5. Report Jatuh Tempo
    Route::get('/report-jatuh-tempo', ReportJatuhTempo::class)->name('report.jatuh-tempo')->middleware('check.role:spv2,spv1');

    // 6. Quick demo role switcher (local development only)
    Route::post('/demo/switch-role/{role}', function (string $role) {
        if (app()->environment() !== 'local') {
            abort(404);
        }

        $user = User::where('role', $role)->first();
        if ($user) {
            Auth::login($user);
        }

        return back();
    })->name('demo.switch-role');

    // 7. Logout
    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
