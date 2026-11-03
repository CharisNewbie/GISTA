<?php

namespace App\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Allowed roles are passed as comma-separated middleware arguments.
     *
     * Example: CheckRole:operator,checker
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        // Role groups: satu nama role boleh mencakup beberapa varian role di database.
        $roleGroups = [
            'operator' => ['operator', 'operator_kp_cabang'],
            'checker' => ['checker'],
            'spv1' => ['spv1', 'supervisor_eksternal'],
            'spv2' => ['spv2', 'supervisor_psv', 'supervisor_txb'],
        ];

        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, $roleGroups[$role] ?? [$role]);
        }
        $allowedRoles = array_unique($allowedRoles);

        if (!$user || !in_array($user->role, $allowedRoles, true)) {
            $previous = url()->previous();
            $redirect = ($previous && $previous !== url()->current())
                ? redirect()->back()
                : redirect()->route('dashboard');

            return $redirect->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
