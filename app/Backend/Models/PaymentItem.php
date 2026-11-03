<?php

namespace App\Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentItem extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'payment_document_id',
        'line_item_po',
        'item_code',
        'item_name',
        'dc_number',
        'ppn_code',
        'pph_code',
        'nominal',
        'status_dc',
        'transaction_type',
        'slid',
        'coa',
        'coa_name',
        'sub_coa',
        'product_code',
        'cost_center',
        'cost_center_name',
        'budget_code',
        'is_selected',
        'delay_start_date',
        'delay_end_date',
    ];

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
