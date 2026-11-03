<?php

namespace App\Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsgRecord extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'payment_document_id',
        'esg_type',
        'cost_center',
        'unit',
        'usage_amount',
        'unit_price',
        'total_amount',
        'billing_period',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'usage_amount' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function paymentDocument(): BelongsTo
    {
        return $this->belongsTo(PaymentDocument::class);
    }
}
