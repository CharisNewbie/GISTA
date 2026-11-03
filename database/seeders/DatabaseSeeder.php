<?php

namespace Database\Seeders;

use App\Backend\Models\AdditionalCost;
use App\Backend\Models\CostAllocation;
use App\Backend\Models\DocumentAttachment;
use App\Backend\Models\EsgRecord;
use App\Backend\Models\PaymentDocument;
use App\Backend\Models\PaymentItem;
use App\Backend\Models\TaxInvoice;
use App\Backend\Models\User;
use App\Backend\Models\WorkflowLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $operator = User::create([
            'name' => 'Operator Kanwil 1',
            'email' => 'operator@bca.co.id',
            'password' => Hash::make('password'),
            'nip' => '97000074',
            'eselon' => 'S6',
            'role' => 'operator',
            'unit_kerja' => 'KEPALA KANWIL KANWIL I (BANDUNG)',
            'kode_cabang' => '0971',
        ]);

        $checker = User::create([
            'name' => 'Kabiro Pengadaan Logistik',
            'email' => 'checker@bca.co.id',
            'password' => Hash::make('password'),
            'nip' => 'S30998031',
            'eselon' => 'S3 1',
            'role' => 'checker',
            'unit_kerja' => 'KABIRO PENGADAAN LOGISTIK',
            'kode_cabang' => '0998',
        ]);

        $spv1 = User::create([
            'name' => 'Rupiyanto',
            'email' => 'spv1@bca.co.id',
            'password' => Hash::make('password'),
            'nip' => 'u932337',
            'eselon' => 'S4',
            'role' => 'spv1',
            'unit_kerja' => 'KAUR ADM PEMBAYARAN & ANGGARAN',
            'kode_cabang' => '0971',
        ]);

        $spv2 = User::create([
            'name' => 'Yohanes Ade Marga Dwi Putra',
            'email' => 'spv2@bca.co.id',
            'password' => Hash::make('password'),
            'nip' => 'u063775',
            'eselon' => 'S7A',
            'role' => 'spv2',
            'unit_kerja' => 'BIRO PENGOLAHAN PEMBAYARAN KEMITRAAN',
            'kode_cabang' => '0998',
        ]);

        // 2. Document 1: OPTG25000460 (Waiting for Approval 1)
        $doc1 = PaymentDocument::create([
            'optg_number' => 'OPTG25000460',
            'po_type' => 'PO Standar',
            'po_number' => 'POST25001876',
            'po_termin_type' => 'PO Non Termin',
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT. GISTA MAJU BERSAMA',
            'description' => 'Pembayaran Pengadaan Barang Logistik',
            'currency' => 'IDR',
            'status' => 'Waiting for Approval 1',
            'payment_status' => 'Payment In Process',
            'document_completeness' => 'Pending',
            'due_date' => now()->addDays(5)->toDateString(),
            'has_due_date_request' => true,
            'requested_due_date' => now()->addDays(14)->toDateString(),
            'delay_reason' => 'Urgensi Tagihan Vendor',
            'delay_notes' => 'Permohonan pembayaran sesuai terms vendor',
            'vendor_invoice_number' => 'LMA08.2025-1023/01',
            'vendor_invoice_date' => now()->subDays(2)->toDateString(),
            'dpp_account_number' => '983475437634967',
            'dpp_account_name' => 'Abi - PT Gista Maju',
            'dpp_bank_name' => 'PT. BANK CENTRAL ASIA Tbk.',
            'ppn_account_number' => '983475437634967',
            'ppn_account_name' => 'Abi - PT Gista Maju',
            'ppn_bank_name' => 'PT. BANK CENTRAL ASIA Tbk.',
            'total_dpp' => 5500000.00,
            'total_ppn' => 605000.00,
            'total_pph' => 330000.00,
            'total_payment' => 5775000.00,
            'payment_notes' => 'Pembayaran tagihan termin 1 logistik',
            'created_by' => $operator->id,
            'spv1_id' => $spv1->id,
            'last_modified_by' => '97000074 - operator kanwil 1',
            'submitted_at' => now()->subDay(),
        ]);

        // Items for Doc 1
        PaymentItem::create([
            'payment_document_id' => $doc1->id,
            'line_item_po' => '1.0-1',
            'item_code' => 'B0000637',
            'item_name' => 'Barang Biaya',
            'dc_number' => 'DC25001876-0001',
            'ppn_code' => 'M1 (11%)',
            'pph_code' => '21C - Non NPWP',
            'nominal' => 1500000.00,
            'status_dc' => 'Fully Received',
            'transaction_type' => 'Pembebanan Langsung',
            'coa' => '691084',
            'coa_name' => 'Biaya Angkutan',
            'sub_coa' => '00',
            'product_code' => '000',
            'cost_center' => 'C00008600',
            'cost_center_name' => 'SUPPORT KCU BANDUNG',
            'is_selected' => true,
        ]);
        PaymentItem::create([
            'payment_document_id' => $doc1->id,
            'line_item_po' => '1.0-2',
            'item_code' => 'B0000637',
            'item_name' => 'Barang Biaya',
            'dc_number' => 'DC25001876-0002',
            'ppn_code' => 'M1 (11%)',
            'pph_code' => '21C - Non NPWP',
            'nominal' => 2000000.00,
            'status_dc' => 'Fully Received',
            'transaction_type' => 'Pembebanan Langsung',
            'coa' => '691084',
            'coa_name' => 'Biaya Angkutan',
            'sub_coa' => '00',
            'product_code' => '000',
            'cost_center' => 'C00008600',
            'cost_center_name' => 'SUPPORT KCU BANDUNG',
            'is_selected' => true,
        ]);
        PaymentItem::create([
            'payment_document_id' => $doc1->id,
            'line_item_po' => '1.0-3',
            'item_code' => 'B0000637',
            'item_name' => 'Barang Biaya',
            'dc_number' => 'DC25001876-0003',
            'ppn_code' => 'M1 (11%)',
            'pph_code' => '21C - Non NPWP',
            'nominal' => 2000000.00,
            'status_dc' => 'Fully Received',
            'transaction_type' => 'Pembebanan Langsung',
            'coa' => '691084',
            'coa_name' => 'Biaya Angkutan',
            'sub_coa' => '00',
            'product_code' => '000',
            'cost_center' => 'C00008600',
            'cost_center_name' => 'SUPPORT KCU BANDUNG',
            'is_selected' => true,
        ]);

        // Tax Invoice for Doc 1
        TaxInvoice::create([
            'payment_document_id' => $doc1->id,
            'type' => 'Standar',
            'invoice_number' => '1234-5678-9101-11213',
            'invoice_date' => now()->toDateString(),
            'currency' => 'IDR',
            'tax_rate' => 0,
            'dpp_idr' => 5500000.00,
            'ppn_idr' => 605000.00,
        ]);

        // Attachments for Doc 1
        DocumentAttachment::create([
            'payment_document_id' => $doc1->id,
            'doc_type' => 'Invoice Vendor',
            'file_path' => 'attachments/invoice_vendor.pdf',
            'file_name' => 'invoice_vendor.pdf',
            'notes' => 'Invoice resmi bermaterai',
            'uploaded_by' => '97000074 - operator kanwil 1',
        ]);

        // Workflow for Doc 1
        WorkflowLog::create([
            'payment_document_id' => $doc1->id,
            'user_name' => 'Operator Kanwil 1 - 97000074 - S6',
            'user_eselon' => 'S6',
            'status' => 'Submitted',
            'notes' => 'Pengajuan pembayaran tagihan PO Standar',
            'created_at' => now()->subDay(),
        ]);
        WorkflowLog::create([
            'payment_document_id' => $doc1->id,
            'user_name' => 'Rupiyanto - u932337 - S4',
            'user_eselon' => 'S4',
            'status' => 'Waiting for Approval 1',
            'notes' => 'Menunggu verifikasi Supervisor Unit Kerja',
            'created_at' => now()->subDay()->addMinutes(1),
        ]);

        // 3. Document 2: OPTG25000376 (Paid - with Bukti Bayar BCA)
        $doc2 = PaymentDocument::create([
            'optg_number' => 'OPTG25000376',
            'po_type' => 'PO Standar',
            'po_number' => 'POST25001796',
            'po_termin_type' => 'PO Non Termin',
            'vendor_code' => '1000027347',
            'vendor_name' => 'STELLA / ARDI DGT',
            'description' => 'Testing Hafizh Otomasi',
            'currency' => 'IDR',
            'status' => 'Approved',
            'payment_status' => 'Paid',
            'document_completeness' => 'Completed',
            'due_date' => now()->subDays(3)->toDateString(),
            'vendor_invoice_number' => 'INV/2025/0904',
            'vendor_invoice_date' => now()->subDays(5)->toDateString(),
            'dpp_account_number' => '1000027347',
            'dpp_account_name' => 'STELLA',
            'dpp_bank_name' => 'PT. BANK CENTRAL ASIA Tbk.',
            'ppn_account_number' => '1000027347',
            'ppn_account_name' => 'STELLA',
            'ppn_bank_name' => 'PT. BANK CENTRAL ASIA Tbk.',
            'total_dpp' => 300000.00,
            'total_ppn' => 0.00,
            'total_payment' => 300000.00,
            'payment_notes' => 'FLO/2/2025',
            'created_by' => $operator->id,
            'spv1_id' => $spv1->id,
            'spv2_id' => $spv2->id,
            'last_modified_by' => '00063775 - STERLING JOHNSTON',
            'submitted_at' => now()->subDays(4),
            'approved_at' => now()->subDays(3),
            'paid_at' => now()->subDays(3),
        ]);

        WorkflowLog::create([
            'payment_document_id' => $doc2->id,
            'user_name' => 'Operator Kanwil 1 - 97000074 - S6',
            'user_eselon' => 'S6',
            'status' => 'Submitted',
            'notes' => 'Pengajuan pembayaran',
            'created_at' => now()->subDays(4),
        ]);
        WorkflowLog::create([
            'payment_document_id' => $doc2->id,
            'user_name' => 'Rupiyanto - u932337 - S4',
            'user_eselon' => 'S4',
            'status' => 'Approved',
            'notes' => 'Disetujui SPV Unit Kerja',
            'created_at' => now()->subDays(3)->subHours(5),
        ]);
        WorkflowLog::create([
            'payment_document_id' => $doc2->id,
            'user_name' => 'Yohanes Ade Marga - u063775 - S7A',
            'user_eselon' => 'S7A',
            'status' => 'Approved',
            'notes' => 'Disetujui Biro Payment (Complete)',
            'created_at' => now()->subDays(3)->subHours(2),
        ]);

        // 4. Document 3: OPTG24001380 (Waiting for Approval 2)
        $doc3 = PaymentDocument::create([
            'optg_number' => 'OPTG24001380',
            'po_type' => 'PO Standar',
            'po_number' => 'PORG24000471',
            'po_termin_type' => 'PO Non Termin',
            'vendor_code' => '1000018186',
            'vendor_name' => 'PT. GISTA MAJU BERSAMA',
            'description' => 'Pengadaan Sewa & Maintenance Server Cabang',
            'currency' => 'IDR',
            'status' => 'Waiting for Approval 2',
            'payment_status' => 'Payment In Process',
            'document_completeness' => 'Pending',
            'due_date' => now()->addDay()->toDateString(),
            'total_dpp' => 285423423.00,
            'total_ppn' => 31396577.00,
            'total_payment' => 316820000.00,
            'created_by' => $operator->id,
            'spv1_id' => $spv1->id,
            'last_modified_by' => 'u932337 - Rupiyanto',
            'submitted_at' => now()->subHours(10),
        ]);
    }
}
