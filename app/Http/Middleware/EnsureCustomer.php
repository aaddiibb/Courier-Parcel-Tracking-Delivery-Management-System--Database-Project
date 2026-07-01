<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()?->role !== 'customer') {
            return redirect()->route('access.denied');
        }
        return $next($request);
    }
}
