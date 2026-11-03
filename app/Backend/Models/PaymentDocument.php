<?php

namespace App\Backend\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PaymentDocument extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'optg_number',
        'po_type',
        'po_number',
        'po_termin_type',
        'vendor_code',
        'vendor_name',
        'description',
        'currency',
        'status',
        'payment_status',
        'document_completeness',
        'due_date',
        'has_due_date_request',
        'requested_due_date',
        'delay_reason',
        'delay_notes',
        'vendor_invoice_number',
        'vendor_invoice_date',
        'dpp_account_number',
        'dpp_account_name',
        'dpp_bank_name',
        'ppn_account_number',
        'ppn_account_name',
        'ppn_bank_name',
        'payment_notes',
        'pending_reason',
        'pending_by',
        'pending_at',
        'pending_from_status',
        'returned_from_status',
        'returned_by',
        'returned_at',
        'total_dpp',
        'total_ppn',
        'total_pph',
        'total_additional_cost',
        'total_discount',
        'total_penalty',
        'total_payment',
        'created_by',
        'spv1_id',
        'checker_id',
        'spv2_id',
        'last_modified_by',
        'submitted_at',
        'approved_at',
        'paid_at',
        'sla_due_at',
        'escalated_at',
        'escalated_to',
    ];

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
            'sla_due_at' => 'datetime',
            'escalated_at' => 'datetime',
            'pending_at' => 'datetime',
            'returned_at' => 'datetime',
            'total_dpp' => 'decimal:2',
            'total_ppn' => 'decimal:2',
            'total_pph' => 'decimal:2',
            'total_additional_cost' => 'decimal:2',
            'total_discount' => 'decimal:2',
            'total_penalty' => 'decimal:2',
            'total_payment' => 'decimal:2',
        ];
    }

    /**
     * Batasi query dokumen sesuai hak akses user.
     *
     * - SPV 2 / Biro Payment : semua dokumen
     * - Operator             : hanya dokumen yang ia buat
     * - Checker / SPV 1      : hanya dokumen yang ditugaskan kepadanya
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->where('id', '<', 0);
        }

        if ($user->isSpv2()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('spv1_id', $user->id)
              ->orWhere('checker_id', $user->id);
        });
    }

    /**
     * Apakah user berhak melihat dokumen ini (mencegah IDOR).
     */
    public function canBeViewedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isSpv2()) {
            return true;
        }

        return $user->id === $this->created_by
            || $user->id === $this->spv1_id
            || $user->id === $this->checker_id;
    }

    /**
     * Apakah user berhak melakukan approve/reject/pending pada dokumen ini.
     * Hanya approver yang benar-benar ditugaskan pada dokumen yang boleh.
     */
    public function canBeActedOnBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $checker = $this->checker;
        $spv1 = $this->spv1;

        $checkerAllowed = $user->isChecker()
            && ($this->checker_id === $user->id
                || ($checker && $checker->isDelegateActive() && $checker->delegate_to_user_id === $user->id));

        $spv1Allowed = $user->isSpv1()
            && ($this->spv1_id === $user->id
                || ($spv1 && $spv1->isDelegateActive() && $spv1->delegate_to_user_id === $user->id));

        return match ($this->status) {
            'Waiting for Checker' => $checkerAllowed,
            'Waiting for Approval 1' => $spv1Allowed,
            'Waiting for Approval 2' => $user->isSpv2(),
            'Pending' => $user->isSpv2(),
            default => false,
        };
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

    public function pendingBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pending_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function isOverdue(): bool
    {
        return $this->sla_due_at !== null
            && $this->sla_due_at->isPast()
            && ! in_array($this->status, ['Approved', 'Paid', 'Cancelled', 'Deleted'], true);
    }

    public function canBeEditedBy(?User $user): bool
    {
        return $user !== null
            && $this->status === 'Correction'
            && $user->isOperator()
            && $this->created_by === $user->id;
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
        // ponytail: DB lock + retry up to 3 attempts; upgrade to distributed lock (Redis) when horizontal scaling needed
        $year = date('y');
        $attempts = 0;
        $maxAttempts = 3;

        while ($attempts < $maxAttempts) {
            try {
                return DB::transaction(function () use ($year) {
                    $lastDoc = self::where('optg_number', 'like', "OPTG{$year}%")
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($lastDoc) {
                        $lastSeq = (int) substr($lastDoc->optg_number, 6);
                        $seq = str_pad($lastSeq + 1, 6, '0', STR_PAD_LEFT);
                    } else {
                        $seq = '000461';
                    }

                    return "OPTG{$year}{$seq}";
                });
            } catch (\Exception $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    throw $e;
                }
                usleep(50000); // 50ms delay before retry
            }
        }

        throw new \RuntimeException('Failed to generate OPTG number after ' . $maxAttempts . ' attempts');
    }
}
