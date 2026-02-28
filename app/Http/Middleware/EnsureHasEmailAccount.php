<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasEmailAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()
            && Schema::hasTable('email_accounts')
            && $request->user()->emailAccounts()->count() === 0
        ) {
            return redirect()->route('account-setup');
        }

        return $next($request);
    }
}
