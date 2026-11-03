<?php

namespace App\Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAttachment extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'payment_document_id',
        'doc_type',
        'file_path',
        'file_name',
        'notes',
        'uploaded_by',
    ];

    public function paymentDocument(): BelongsTo
    {
        return $this->belongsTo(PaymentDocument::class);
    }
}
