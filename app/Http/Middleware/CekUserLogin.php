<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role = "all")
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        // switch ($role) {
        //     case 'admin':
        //         if (Auth::user()->level > 3) {
        //             return redirect('/');
        //         }
        //         break;
        //     case 'penelitiDokumen':
        //         if (Auth::user()->level == 4) {
        //             return redirect('/');
        //         }
        //         break;
        //     case 'pemeriksaBarang':
        //         if (Auth::user()->level == 3) {
        //             return redirect('/');
        //         }
        //         break;
        // }
        return $next($request);
    }
}
