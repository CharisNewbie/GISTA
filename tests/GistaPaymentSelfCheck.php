<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Livewire\Gista\BuktiBayar;
use App\Models\PaymentDocument;
use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "--- GISTA PAYMENT SELF-CHECK ---\n";

// 1. Check Seeded Users
$operator = User::where('email', 'operator@bca.co.id')->first();
assert($operator !== null, "Operator user must exist");
assert($operator->role === 'operator', "Operator role must be operator");

$spv1 = User::where('email', 'spv1@bca.co.id')->first();
assert($spv1 !== null, "SPV 1 must exist");
assert($spv1->isSpv1(), "User must identify as SPV 1");

$spv2 = User::where('email', 'spv2@bca.co.id')->first();
assert($spv2 !== null, "SPV 2 must exist");
assert($spv2->isSpv2(), "User must identify as SPV 2");
echo "✓ Users and roles verified\n";

// 2. Check Document OPTG generator
$optg = PaymentDocument::generateOptgNumber();
assert(str_starts_with($optg, 'OPTG'), "OPTG number must start with OPTG");
assert(strlen($optg) === 12, "OPTG number length must be 12 chars (e.g. OPTG26000461)");
echo "✓ OPTG sequence generator: {$optg}\n";

// 3. Check Terbilang helper in BuktiBayar
assert(BuktiBayar::terbilang(300000) === 'Tiga Ratus Ribu', "Terbilang 300000 must be 'Tiga Ratus Ribu'");
assert(BuktiBayar::terbilang(5500000) === 'Lima Juta Lima Ratus Ribu', "Terbilang 5500000 must be 'Lima Juta Lima Ratus Ribu'");
echo "✓ Terbilang Indonesian currency converter verified\n";

// 4. Check Seeded Documents & Workflow logs
$doc1 = PaymentDocument::where('optg_number', 'OPTG25000460')->first();
assert($doc1 !== null, "Document OPTG25000460 must exist");
assert($doc1->items()->count() === 3, "OPTG25000460 must have 3 DC items");
assert($doc1->taxInvoices()->count() >= 1, "OPTG25000460 must have tax invoice");
assert($doc1->workflowLogs()->count() >= 2, "OPTG25000460 must have workflow audit trail");
echo "✓ Document relationships and workflow audit trail verified\n";

// 5. Check Paid Document for Bukti Bayar
$doc2 = PaymentDocument::where('optg_number', 'OPTG25000376')->first();
assert($doc2 !== null, "Document OPTG25000376 must exist");
assert($doc2->payment_status === 'Paid', "OPTG25000376 must be Paid");
echo "✓ Bukti Bayar candidate verified\n";

// 6. Check Routes
$requiredRoutes = [
    'payment.index',
    'payment.select-type',
    'payment.create',
    'payment.show',
    'payment.bukti-bayar',
    'inbox',
    'dashboard.payment',
    'report.jatuh-tempo',
    'demo.switch-role',
];
foreach ($requiredRoutes as $routeName) {
    assert(Route::has($routeName), "Route {$routeName} must be registered");
}
echo "✓ All " . count($requiredRoutes) . " GISTA Payment routes registered\n";

echo "ALL CHECKS PASSED!\n";
