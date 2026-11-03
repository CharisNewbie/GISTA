<?php

namespace App\Backend\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Backend\Models\DocumentAttachment;
use App\Backend\Models\PaymentDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AttachmentDownloadController extends Controller
{
    /**
     * MIME types yang aman ditampilkan inline di browser (preview).
     * Tipe lain tetap dipaksa download agar tidak bisa dieksekusi sebagai HTML/script.
     */
    private const PREVIEWABLE_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
    ];

    public function __invoke(Request $request, DocumentAttachment $attachment): Response|RedirectResponse
    {
        return $this->fileResponse($request, $attachment, inline: false);
    }

    public function preview(Request $request, DocumentAttachment $attachment): Response|RedirectResponse
    {
        return $this->fileResponse($request, $attachment, inline: true);
    }

    private function fileResponse(Request $request, DocumentAttachment $attachment, bool $inline): Response|RedirectResponse
    {
        $document = PaymentDocument::findOrFail($attachment->payment_document_id);

        // Authorization: hanya pembuat, approver yang ditugaskan, atau SPV 2.
        $canView = $document->canBeViewedBy($request->user());

        if (!$canView) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses Ditolak: Anda tidak memiliki akses ke dokumen lampiran ini.');
        }

        $disk = Storage::disk('local');

        if (!$disk->exists($attachment->file_path)) {
            return redirect()->route('dashboard')
                ->with('error', 'File lampiran tidak ditemukan.');
        }

        $mimeType = $disk->mimeType($attachment->file_path) ?: 'application/octet-stream';

        // Fallback ke download jika tipe file tidak aman untuk ditampilkan inline.
        if ($inline && in_array($mimeType, self::PREVIEWABLE_MIME_TYPES, true)) {
            return $disk->response($attachment->file_path, $attachment->file_name, [
                'Content-Type' => $mimeType,
            ], 'inline');
        }

        return $disk->download($attachment->file_path, $attachment->file_name, [
            'Content-Type' => $mimeType,
        ]);
    }
}
