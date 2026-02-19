<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictScanGateAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'isScanGate') || !$user->isScanGate()) {
            return $next($request);
        }

        $allowedRouteNames = [
            'home',
            'scan.mobile',
            'scan.do',
            'logout',
            'admin.impersonate.stop',
        ];

        $routeName = $request->route()?->getName();
        if (in_array($routeName, $allowedRouteNames, true)) {
            return $next($request);
        }

        return redirect()->route('scan.mobile');
    }
}
