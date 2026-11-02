<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add BCA specific role/profile columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->string('eselon')->nullable()->after('nip');
            $table->string('role')->default('operator'); // operator, checker, spv1, spv2
            $table->string('unit_kerja')->nullable()->after('role');
            $table->string('kode_cabang')->nullable()->after('unit_kerja');
        });

        // Main Payment Documents (OPTG)
        Schema::create('payment_documents', function (Blueprint $table) {
            $table->id();
            $table->string('optg_number')->unique(); // OPTGYYXXXXXX
            $table->string('po_type')->default('PO Standar'); // PO Standar, PO Register, PO Sewa
            $table->string('po_number'); // POST25001876
            $table->string('po_termin_type')->default('PO Non Termin');
            $table->string('vendor_code'); // 1000018186
            $table->string('vendor_name'); // PT. GISTA MAJU BERSAMA
            $table->text('description')->nullable();
            $table->string('currency')->default('IDR');

            // Statuses according to PDF
            // Draft, Deleted, Waiting for Checker, Waiting for Approval 1, Waiting for Approval 2, Correction, Pending, Approved, Waiting for Cancellation, Cancelled
            $table->string('status')->default('Draft');
            $table->string('payment_status')->default('Payment In Process'); // Payment In Process, Payment Failed, Paid
            $table->string('document_completeness')->default('Pending'); // Pending, Completed, Incomplete

            // Due Date & Timing
            $table->date('due_date');
            $table->boolean('has_due_date_request')->default(false);
            $table->date('requested_due_date')->nullable();
            $table->string('delay_reason')->nullable();
            $table->string('delay_notes')->nullable();

            // Vendor Invoice
            $table->string('vendor_invoice_number')->nullable();
            $table->date('vendor_invoice_date')->nullable();

            // Bank Accounts
            $table->string('dpp_account_number')->nullable();
            $table->string('dpp_account_name')->nullable();
            $table->string('dpp_bank_name')->default('PT. BANK CENTRAL ASIA Tbk.');
            $table->string('dpp_swift_code')->nullable();

            $table->string('ppn_account_number')->nullable();
            $table->string('ppn_account_name')->nullable();
            $table->string('ppn_bank_name')->default('PT. BANK CENTRAL ASIA Tbk.');
            $table->string('ppn_swift_code')->nullable();

            // Financial Summary
            $table->decimal('total_dpp', 15, 2)->default(0);
            $table->decimal('total_ppn', 15, 2)->default(0);
            $table->decimal('total_pph', 15, 2)->default(0);
            $table->decimal('total_additional_cost', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('total_penalty', 15, 2)->default(0);
            $table->decimal('total_payment', 15, 2)->default(0);

            $table->string('payment_notes', 100)->nullable();

            // Roles involved
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('checker_id')->nullable()->constrained('users');
            $table->foreignId('spv1_id')->nullable()->constrained('users');
            $table->foreignId('spv2_id')->nullable()->constrained('users');
            $table->string('last_modified_by')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Delivery Check / Items from PO
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained()->cascadeOnDelete();
            $table->string('line_item_po')->default('1.0-1');
            $table->string('item_code')->default('B0000637');
            $table->string('item_name')->default('Barang Biaya');
            $table->string('dc_number'); // DC25001876-0001
            $table->string('ppn_code')->default('M1');
            $table->string('pph_code')->nullable()->default('21C - Non NPWP');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('status_dc')->default('Fully Received');
            $table->string('transaction_type')->default('Pembebanan Langsung'); // Pembebanan Langsung, Reimbursement, Penundaan Biaya
            $table->string('slid')->nullable();
            $table->string('coa')->default('691084');
            $table->string('coa_name')->default('Biaya Angkutan');
            $table->string('sub_coa')->default('00');
            $table->string('sub_coa_name')->default('Default');
            $table->string('product_code')->default('000');
            $table->string('cost_center')->default('C00008600');
            $table->string('cost_center_name')->default('SUPPORT KCU BANDUNG');
            $table->string('budget_code')->nullable();

            // Reimbursement details if applicable
            $table->string('reimbursement_type')->nullable(); // RRA, Antar Kantor
            $table->string('reimbursement_budget_type')->nullable(); // Kode Budget, Cost Center
            $table->string('reimbursement_code')->nullable();
            $table->string('reimbursement_unit')->nullable();

            // Delay / Penundaan details
            $table->date('delay_start_date')->nullable();
            $table->date('delay_end_date')->nullable();

            $table->boolean('is_selected')->default(true);
            $table->timestamps();
        });

        // Faktur Pajak
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('Standar'); // Standar, Dipersamakan
            $table->string('invoice_number'); // 16-17 digit or text
            $table->date('invoice_date');
            $table->string('currency')->default('IDR');
            $table->decimal('tax_rate', 10, 2)->default(0);
            $table->decimal('dpp_valas', 15, 2)->default(0);
            $table->decimal('ppn_valas', 15, 2)->default(0);
            $table->decimal('dpp_idr', 15, 2)->default(0);
            $table->decimal('ppn_idr', 15, 2)->default(0);
            $table->timestamps();
        });

        // Pembebanan Tambahan (Biaya Tambahan / Potongan / Denda)
        Schema::create('additional_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // Biaya Tambahan (Db), Potongan (Kr), Denda (Kr)
            $table->string('coa')->default('691144');
            $table->string('coa_name')->default('Biaya Pengelola Pendukung Operasional');
            $table->string('sub_coa')->default('00');
            $table->string('slid')->nullable();
            $table->string('product_code')->default('000');
            $table->string('cost_center')->default('C00998031');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });

        // Alokasi Biaya (Step 5)
        Schema::create('cost_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained()->cascadeOnDelete();
            $table->string('item_description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('transaction_type')->default('Pembebanan Langsung');
            $table->string('budget_type')->default('Cost Center'); // Cost Center, Kode Budget
            $table->string('cost_center')->nullable();
            $table->string('budget_code')->nullable();
            $table->string('coa')->nullable();
            $table->string('sub_coa')->nullable();
            $table->string('slid')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // ESG Records (Step 6)
        Schema::create('esg_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained()->cascadeOnDelete();
            $table->string('esg_type'); // Listrik, Air, Angkutan, Bahan Bakar
            $table->string('cost_center')->default('C00998031');
            $table->string('unit'); // kwh, liter, M3
            $table->decimal('usage_amount', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('fuel_type')->nullable(); // Pertamax, Pertalite, Solar
            $table->string('billing_period')->default('07/2024');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // Attachments
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained()->cascadeOnDelete();
            $table->string('doc_type')->default('Invoice Vendor'); // Invoice Vendor, Dokumen Pendukung Lainnya
            $table->string('file_path');
            $table->string('file_name');
            $table->string('notes')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->timestamps();
        });

        // Workflow Audit Trail (PDF Page 39)
        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained()->cascadeOnDelete();
            $table->string('user_name');
            $table->string('user_eselon')->nullable();
            $table->string('status'); // Submitted, Waiting for Approval 1, Approved, Correction, Pending, Cancelled
            $table->string('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('document_attachments');
        Schema::dropIfExists('esg_records');
        Schema::dropIfExists('cost_allocations');
        Schema::dropIfExists('additional_costs');
        Schema::dropIfExists('tax_invoices');
        Schema::dropIfExists('payment_items');
        Schema::dropIfExists('payment_documents');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'eselon', 'role', 'unit_kerja', 'kode_cabang']);
        });
    }
};
