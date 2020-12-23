<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() && !Auth::user()->isAdmin()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
