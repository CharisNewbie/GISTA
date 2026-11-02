<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDocument extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'requested_due_date' => 'date',
            'vendor_invoice_date' => 'date',
            'has_due_date_request' => 'boolean',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
            'total_dpp' => 'decimal:2',
            'total_ppn' => 'decimal:2',
            'total_pph' => 'decimal:2',
            'total_additional_cost' => 'decimal:2',
            'total_discount' => 'decimal:2',
            'total_penalty' => 'decimal:2',
            'total_payment' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function spv1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'spv1_id');
    }

    public function spv2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'spv2_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function taxInvoices(): HasMany
    {
        return $this->hasMany(TaxInvoice::class);
    }

    public function additionalCosts(): HasMany
    {
        return $this->hasMany(AdditionalCost::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(CostAllocation::class);
    }

    public function esgRecords(): HasMany
    {
        return $this->hasMany(EsgRecord::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    public function workflowLogs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class)->orderBy('created_at', 'asc');
    }

    public function logWorkflow(string $status, ?string $notes = null, ?User $user = null): WorkflowLog
    {
        $user = $user ?? auth()->user();
        return $this->workflowLogs()->create([
            'user_name' => $user ? ($user->name . ' - ' . ($user->nip ?? 'User') . ' - ' . ($user->eselon ?? '')) : 'System',
            'user_eselon' => $user?->eselon,
            'status' => $status,
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }

    public static function generateOptgNumber(): string
    {
        $year = date('y');
        $lastDoc = self::where('optg_number', 'like', "OPTG{$year}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDoc) {
            $lastSeq = (int) substr($lastDoc->optg_number, 6);
            $seq = str_pad($lastSeq + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $seq = '000461';
        }

        return "OPTG{$year}{$seq}";
    }
}
