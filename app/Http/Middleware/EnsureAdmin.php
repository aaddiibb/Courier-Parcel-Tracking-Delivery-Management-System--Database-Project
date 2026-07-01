<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()?->role !== 'admin') {
            return redirect()->route('access.denied');
        }
        return $next($request);
    }
}
