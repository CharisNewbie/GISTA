<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->timestamp('sla_due_at')->nullable()->after('submitted_at');
            $table->timestamp('escalated_at')->nullable()->after('sla_due_at');
            $table->foreignId('escalated_to')->nullable()->after('escalated_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('delegate_to_user_id')->nullable()->after('kode_cabang')->constrained('users')->nullOnDelete();
            $table->timestamp('delegate_start')->nullable()->after('delegate_to_user_id');
            $table->timestamp('delegate_end')->nullable()->after('delegate_start');
        });
    }

    public function down(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropForeign(['escalated_to']);
            $table->dropColumn(['sla_due_at', 'escalated_at', 'escalated_to']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['delegate_to_user_id']);
            $table->dropColumn(['delegate_to_user_id', 'delegate_start', 'delegate_end']);
        });
    }
};
