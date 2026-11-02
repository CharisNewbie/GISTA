<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'is_selected' => 'boolean',
            'delay_start_date' => 'date',
            'delay_end_date' => 'date',
        ];
    }

    public function paymentDocument(): BelongsTo
    {
        return $this->belongsTo(PaymentDocument::class);
    }
}
