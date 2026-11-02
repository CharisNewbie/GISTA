<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxInvoice extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'tax_rate' => 'decimal:2',
            'dpp_valas' => 'decimal:2',
            'ppn_valas' => 'decimal:2',
            'dpp_idr' => 'decimal:2',
            'ppn_idr' => 'decimal:2',
        ];
    }

    public function paymentDocument(): BelongsTo
    {
        return $this->belongsTo(PaymentDocument::class);
    }
}
