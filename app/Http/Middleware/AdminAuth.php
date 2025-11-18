<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin_logged_in')) {
            return redirect()->route('login')->withErrors([
                'login' => 'Silakan login terlebih dahulu.'
            ]);
        }

        return $next($request);
    }
}
