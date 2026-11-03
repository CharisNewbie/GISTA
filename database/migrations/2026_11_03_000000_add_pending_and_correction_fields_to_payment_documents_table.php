<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            // Pending (ditahan sementara oleh SPV2)
            $table->text('pending_reason')->nullable()->after('payment_notes');
            $table->foreignId('pending_by')->nullable()->after('pending_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('pending_at')->nullable()->after('pending_by');
            $table->string('pending_from_status')->nullable()->after('pending_at');

            // Correction (dikembalikan ke operator untuk perbaikan)
            $table->string('returned_from_status')->nullable()->after('pending_from_status');
            $table->foreignId('returned_by')->nullable()->after('returned_from_status')->constrained('users')->nullOnDelete();
            $table->timestamp('returned_at')->nullable()->after('returned_by');
        });
    }

    public function down(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropForeign(['pending_by']);
            $table->dropForeign(['returned_by']);
            $table->dropColumn([
                'pending_reason',
                'pending_by',
                'pending_at',
                'pending_from_status',
                'returned_from_status',
                'returned_by',
                'returned_at',
            ]);
        });
    }
};
