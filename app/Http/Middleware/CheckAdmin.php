<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //laravel akan mengecek apakah user sudah login atau belum, middleware ini akan mencegah user untuk masuk ke halaman tertentu
        //jika sudah maka akun akan lanjut ke halaman yang dituju,
        //jika belum maka user akan diarahkan ke halaman login dengan pesan error
        if (Auth::check()) {
        return $next($request);
        }else{
            return redirect()->to('auth/login')->with('error', 'Anda harus login terlebih dahulu.');
        }
    }
}
