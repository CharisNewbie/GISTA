<?php

namespace App\Backend\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Backend\Models\DocumentAttachment;
use App\Backend\Models\PaymentDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentDownloadController extends Controller
{
    public function __invoke(Request $request, DocumentAttachment $attachment): BinaryFileResponse|RedirectResponse
    {
        $document = PaymentDocument::findOrFail($attachment->payment_document_id);

        // Authorization: hanya pembuat, approver yang ditugaskan, atau SPV 2.
        $canView = $document->canBeViewedBy($request->user());

        if (!$canView) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses Ditolak: Anda tidak memiliki akses ke dokumen lampiran ini.');
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            return redirect()->route('dashboard')
                ->with('error', 'File lampiran tidak ditemukan.');
        }

        return Storage::disk('local')->download($attachment->file_path, $attachment->file_name);
    }
}
