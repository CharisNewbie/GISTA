<?php

namespace Tests\Feature;

use App\Livewire\Gista\BuktiBayar;
use App\Livewire\Gista\CreatePayment;
use App\Livewire\Gista\PaymentDisplay;
use App\Livewire\Gista\PaymentList;
use App\Livewire\Gista\ReportJatuhTempo;
use App\Models\PaymentDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GistaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected User $spv1;
    protected User $spv2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create([
            'email' => 'operator@bca.co.id',
            'role' => 'operator',
            'name' => 'Operator Test',
            'nip' => '88880001',
        ]);

        $this->spv1 = User::factory()->create([
            'email' => 'spv1@bca.co.id',
            'role' => 'spv1',
            'name' => 'Supervisor 1 Test',
            'nip' => '88880002',
        ]);

        $this->spv2 = User::factory()->create([
            'email' => 'spv2@bca.co.id',
            'role' => 'spv2',
            'name' => 'Supervisor 2 Test',
            'nip' => '88880003',
        ]);
    }

    public function test_operator_can_submit_po_payment_wizard(): void
    {
        $this->actingAs($this->operator);

        Livewire::test(CreatePayment::class)
            ->set('poNumber', 'POST25001876')
            ->call('checkPo')
            ->set('vendorInvoiceNo', 'INV-2026-001')
            ->call('submitPayment')
            ->assertRedirect(route('payment.show', 1));

        $this->assertDatabaseHas('payment_documents', [
            'po_number' => 'POST25001876',
            'vendor_invoice_number' => 'INV-2026-001',
            'status' => 'Waiting for Approval 1',
        ]);
    }

    public function test_spv1_can_approve_document_to_waiting_approval_2(): void
    {
        $this->actingAs($this->operator);

        $doc = PaymentDocument::create([
            'optg_number' => PaymentDocument::generateOptgNumber(),
            'po_number' => 'POST25001876',
            'payment_type' => 'PO Standard',
            'due_date' => now()->addDays(5),
            'status' => 'Waiting for Approval 1',
            'payment_status' => 'Unpaid',
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT. GISTA MAJU BERSAMA',
            'currency' => 'IDR',
            'total_dpp' => 5500000,
            'total_ppn' => 605000,
            'total_payment' => 6105000,
            'created_by' => $this->operator->id,
        ]);

        $this->actingAs($this->spv1);

        Livewire::test(PaymentDisplay::class, ['document' => $doc])
            ->call('openAction', 'approve')
            ->set('actionNotes', 'Disetujui SPV 1 Unit')
            ->call('confirmAction');

        $doc->refresh();
        $this->assertEquals('Waiting for Approval 2', $doc->status);
        $this->assertDatabaseHas('workflow_logs', [
            'payment_document_id' => $doc->id,
            'status' => 'Waiting for Approval 2',
            'notes' => 'Disetujui SPV 1 Unit',
        ]);
    }

    public function test_spv2_can_approve_complete_document(): void
    {
        $doc = PaymentDocument::create([
            'optg_number' => PaymentDocument::generateOptgNumber(),
            'po_number' => 'POST25001876',
            'payment_type' => 'PO Standard',
            'due_date' => now()->addDays(5),
            'status' => 'Waiting for Approval 2',
            'payment_status' => 'Unpaid',
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT. GISTA MAJU BERSAMA',
            'currency' => 'IDR',
            'total_dpp' => 5500000,
            'total_ppn' => 605000,
            'total_payment' => 6105000,
            'created_by' => $this->operator->id,
        ]);

        $this->actingAs($this->spv2);

        Livewire::test(PaymentDisplay::class, ['document' => $doc])
            ->call('openAction', 'approve')
            ->set('completenessStatus', 'Complete')
            ->set('actionNotes', 'Semua berkas lengkap diverifikasi Biro Payment')
            ->call('confirmAction');

        $doc->refresh();
        $this->assertEquals('Approved', $doc->status);
        $this->assertEquals('Completed', $doc->document_completeness);
    }

    public function test_spv1_or_spv2_can_reject_document_to_correction(): void
    {
        $doc = PaymentDocument::create([
            'optg_number' => PaymentDocument::generateOptgNumber(),
            'po_number' => 'POST25001876',
            'payment_type' => 'PO Standard',
            'due_date' => now()->addDays(5),
            'status' => 'Waiting for Approval 1',
            'payment_status' => 'Unpaid',
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT. GISTA MAJU BERSAMA',
            'currency' => 'IDR',
            'total_dpp' => 5500000,
            'total_payment' => 5500000,
            'created_by' => $this->operator->id,
        ]);

        $this->actingAs($this->spv1);

        Livewire::test(PaymentDisplay::class, ['document' => $doc])
            ->call('openAction', 'reject')
            ->set('actionNotes', 'Faktur pajak tidak cocok')
            ->call('confirmAction');

        $doc->refresh();
        $this->assertEquals('Correction', $doc->status);
    }

    public function test_bukti_bayar_terbilang_and_view_rendering(): void
    {
        $doc = PaymentDocument::create([
            'optg_number' => PaymentDocument::generateOptgNumber(),
            'po_number' => 'POST25001876',
            'payment_type' => 'PO Standard',
            'due_date' => now()->addDays(5),
            'status' => 'Approved',
            'payment_status' => 'Paid',
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT. GISTA MAJU BERSAMA',
            'currency' => 'IDR',
            'total_payment' => 6105000,
            'paid_at' => now(),
            'created_by' => $this->operator->id,
        ]);

        $this->actingAs($this->spv2);

        $response = $this->get(route('payment.bukti-bayar', $doc->id));
        $response->assertOk()
            ->assertSee('PT BANK CENTRAL ASIA TBK')
            ->assertSee('TRANSFER BERHASIL')
            ->assertSee('Enam Juta Seratus Lima Ribu Rupiah');
    }
}
