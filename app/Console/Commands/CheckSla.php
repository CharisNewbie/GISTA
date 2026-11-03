<?php

namespace App\Console\Commands;

use App\Backend\Models\PaymentDocument;
use Illuminate\Console\Command;

class CheckSla extends Command
{
    protected $signature = 'gista:check-sla';
    protected $description = 'Cek dokumen yang melewati SLA dan tandai sebagai escalated.';

    public function handle(): int
    {
        $overdue = PaymentDocument::whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->whereNotIn('status', ['Approved', 'Paid', 'Cancelled', 'Deleted'])
            ->get();

        foreach ($overdue as $doc) {
            if ($doc->escalated_at === null) {
                $doc->update(['escalated_at' => now()]);
                $this->info("Escalated: {$doc->optg_number} (status: {$doc->status})");
            }
        }

        $this->info("Total overdue: {$overdue->count()}");
        return self::SUCCESS;
    }
}
