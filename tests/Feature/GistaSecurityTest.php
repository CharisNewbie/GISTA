<?php

namespace Tests\Feature;

use App\Backend\Livewire\Gista\BuktiBayar;
use App\Backend\Livewire\Gista\CreatePayment;
use App\Backend\Livewire\Gista\Inbox;
use App\Backend\Livewire\Gista\PaymentDisplay;
use App\Backend\Livewire\Gista\PaymentList;
use App\Backend\Livewire\Gista\ReportJatuhTempo;
use App\Backend\Models\DocumentAttachment;
use App\Backend\Models\PaymentDocument;
use App\Backend\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GistaSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected User $otherOperator;
    protected User $spv1;
    protected User $otherSpv1;
    protected User $spv2;
    protected User $checker;
    protected User $otherChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = $this->makeUser('operator', 'operator-a@bca.co.id', 'Operator A');
        $this->otherOperator = $this->makeUser('operator', 'operator-b@bca.co.id', 'Operator B');
        $this->spv1 = $this->makeUser('spv1', 'spv1-a@bca.co.id', 'SPV1 A');
        $this->otherSpv1 = $this->makeUser('spv1', 'spv1-b@bca.co.id', 'SPV1 B');
        $this->spv2 = $this->makeUser('spv2', 'spv2-a@bca.co.id', 'SPV2 A');
        $this->checker = $this->makeUser('checker', 'checker-a@bca.co.id', 'Checker A');
        $this->otherChecker = $this->makeUser('checker', 'checker-b@bca.co.id', 'Checker B');
    }

    private function makeUser(string $role, string $email, string $name): User
    {
        return User::factory()->create([
            'role' => $role,
            'email' => $email,
            'name' => $name,
            'nip' => (string) fake()->unique()->numerify('8888####'),
        ]);
    }

    private function makeDocument(array $overrides = []): PaymentDocument
    {
        return PaymentDocument::create(array_merge([
            'optg_number' => PaymentDocument::generateOptgNumber(),
            'po_number' => 'POST25001876',
            'po_type' => 'PO Standar',
            'due_date' => now()->addDays(5),
            'status' => 'Waiting for Approval 1',
            'payment_status' => 'Payment In Process',
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT. GISTA MAJU BERSAMA',
            'currency' => 'IDR',
            'total_dpp' => 5500000,
            'total_ppn' => 605000,
            'total_payment' => 6105000,
            'created_by' => $this->operator->id,
            'spv1_id' => $this->spv1->id,
        ], $overrides));
    }

    public function test_operator_cannot_view_another_operators_document(): void
    {
        $doc = $this->makeDocument(['created_by' => $this->otherOperator->id]);

        Livewire::actingAs($this->operator)
            ->test(PaymentDisplay::class, ['document' => $doc])
            ->assertRedirect(route('dashboard'));
    }

    public function test_spv1_cannot_approve_document_assigned_to_other_spv1(): void
    {
        $doc = $this->makeDocument([
            'created_by' => $this->spv1->id,
            'spv1_id' => $this->otherSpv1->id,
        ]);

        Livewire::actingAs($this->spv1)
            ->test(PaymentDisplay::class, ['document' => $doc])
            ->call('openAction', 'approve')
            ->assertSet('showActionModal', false)
            ->assertSet('authError', 'Anda tidak ditugaskan atau tidak berwenang melakukan tindakan ini pada dokumen ini.');

        $this->assertSame('Waiting for Approval 1', $doc->fresh()->status);
    }

    public function test_assigned_spv1_can_still_approve(): void
    {
        $doc = $this->makeDocument(['spv1_id' => $this->spv1->id]);

        Livewire::actingAs($this->spv1)
            ->test(PaymentDisplay::class, ['document' => $doc])
            ->call('openAction', 'approve')
            ->call('confirmAction');

        $this->assertSame('Waiting for Approval 2', $doc->fresh()->status);
    }

    public function test_checker_inbox_only_shows_documents_assigned_to_them(): void
    {
        $mine = $this->makeDocument([
            'status' => 'Waiting for Checker',
            'checker_id' => $this->checker->id,
            'optg_number' => 'OPTG26000001',
        ]);

        $other = $this->makeDocument([
            'status' => 'Waiting for Checker',
            'checker_id' => $this->otherChecker->id,
            'optg_number' => 'OPTG26000002',
        ]);

        Livewire::actingAs($this->checker)
            ->test(Inbox::class)
            ->assertSee($mine->optg_number)
            ->assertDontSee($other->optg_number);
    }

    public function test_payment_list_only_shows_own_documents_for_operator(): void
    {
        $mine = $this->makeDocument([
            'created_by' => $this->operator->id,
            'optg_number' => 'OPTG26000011',
        ]);

        $other = $this->makeDocument([
            'created_by' => $this->otherOperator->id,
            'optg_number' => 'OPTG26000012',
        ]);

        Livewire::actingAs($this->operator)
            ->test(PaymentList::class)
            ->assertSee($mine->optg_number)
            ->assertDontSee($other->optg_number);
    }

    public function test_bukti_bayar_rejects_unauthorized_document(): void
    {
        $doc = $this->makeDocument(['created_by' => $this->otherOperator->id]);

        Livewire::actingAs($this->operator)
            ->test(BuktiBayar::class, ['document' => $doc])
            ->assertRedirect(route('dashboard'));
    }

    public function test_report_status_modal_rejects_unauthorized_document(): void
    {
        $doc = $this->makeDocument([
            'created_by' => $this->otherOperator->id,
            'spv1_id' => $this->otherSpv1->id,
        ]);

        Livewire::actingAs($this->spv1)
            ->test(ReportJatuhTempo::class)
            ->call('viewStatus', $doc->id)
            ->assertSet('showStatusModal', false)
            ->assertSet('selectedDoc', null)
            ->assertSee('Akses Ditolak');
    }

    public function test_create_payment_rejects_approver_with_wrong_role(): void
    {
        Livewire::actingAs($this->operator)
            ->test(CreatePayment::class)
            ->set('selectedSpv1Id', $this->spv2->id)
            ->call('submitPayment')
            ->assertHasErrors(['selectedSpv1Id']);

        $this->assertDatabaseCount('payment_documents', 0);
    }

    public function test_attachment_download_rejects_unauthorized_user(): void
    {
        $doc = $this->makeDocument([
            'created_by' => $this->otherOperator->id,
            'spv1_id' => $this->otherSpv1->id,
        ]);

        $attachment = DocumentAttachment::create([
            'payment_document_id' => $doc->id,
            'doc_type' => 'Invoice Vendor',
            'file_name' => 'invoice.pdf',
            'file_path' => 'attachments/invoice.pdf',
            'uploaded_by' => 'system',
        ]);

        $this->actingAs($this->operator)
            ->get(route('attachments.download', $attachment->id))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }
}
