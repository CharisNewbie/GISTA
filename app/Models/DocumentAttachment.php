<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAttachment extends Model
{
    protected $guarded = ['id'];

    public function paymentDocument(): BelongsTo
    {
        return $this->belongsTo(PaymentDocument::class);
    }
}
