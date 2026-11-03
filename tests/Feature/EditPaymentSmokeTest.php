<?php
namespace Tests\Feature;
use App\Backend\Models\PaymentDocument;
use App\Backend\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditPaymentSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_renders_for_correction_doc(): void
    {
        $operator = User::factory()->create(['role'=>'operator','email'=>'op@bca.co.id','nip'=>'1111']);
        $doc = PaymentDocument::create([
            'optg_number' => PaymentDocument::generateOptgNumber(),
            'po_number' => 'POST25001876',
            'status' => 'Correction',
            'returned_from_status' => 'Waiting for Approval 1',
            'payment_status' => 'Payment In Process',
            'due_date' => now()->addDays(5),
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT GISTA',
            'currency' => 'IDR',
            'total_dpp' => 1000,
            'total_payment' => 1000,
            'created_by' => $operator->id,
        ]);

        $this->actingAs($operator)
            ->get(route('payment.edit', ['document' => $doc->id]))
            ->assertOk()
            ->assertSee('Mode Perbaikan');
    }
}
