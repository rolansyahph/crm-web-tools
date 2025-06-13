<?php

namespace App\Http\Middleware;

use Closure;

class CheckSession
{
    public function handle($request, Closure $next)
    {
    // Tambahkan kondisi untuk tidak memeriksa sesi jika route adalah logout
    if ($request->routeIs('logout')) {
        return $next($request);
    }

    // Memeriksa sesi untuk route selain logout
    if (!$request->session()->exists('userid')) {
        return redirect('/');
    }
    
    // Jika sesi masih ada dan user mencoba mengakses halaman login, redirect ke dashboard
    // if ($request->session()->exists('username') && $request->is('/')) {
    //     return redirect('/dashboard');
    // }

    return $next($request);
    }
}
