<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $user = User::where('domain', $host)->first();

        if ($user) {
            $schemaName = 'tenant_' . $user->id;

            // Dynamiczne ustawienie połączenia z bazą danych
            config(['database.connections.tenant.database' => $schemaName]);

            DB::purge('tenant');
            DB::reconnect('tenant');
            DB::setDefaultConnection('tenant');
        }

        return $next($request);
    }
}
