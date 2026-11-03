<?php

namespace App\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Attachment preview dibuka di iframe same-origin, jadi harus diizinkan
        // untuk di-frame oleh aplikasi sendiri. Route lain tetap diblokir.
        $isAttachmentPreview = $request->routeIs('attachments.preview');
        $frameAncestors = $isAttachmentPreview ? "'self'" : "'none'";

        // Content Security Policy: only allow same-origin + fonts.bunny.net + Tailwind/Vite assets
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.bunny.net; img-src 'self' data: https:; font-src 'self' https://fonts.bunny.net; connect-src 'self'; frame-src 'self'; object-src 'none'; frame-ancestors {$frameAncestors}; base-uri 'self'; form-action 'self';");

        // Prevent clickjacking (preview lampiran boleh di-frame same-origin)
        $response->headers->set('X-Frame-Options', $isAttachmentPreview ? 'SAMEORIGIN' : 'DENY');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Disable powerful browser features yang tidak dipakai aplikasi.
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Isolasi browsing context
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // HSTS in production (HTTPS only)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
