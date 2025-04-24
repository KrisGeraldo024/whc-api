<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthGlobal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response([
                'errors' => ['Invalid credentials']
            ], 403);
        } else {
            if (Auth::user()) {
                return $next($request);
            } else {
                return response([
                    'errors' => ['You don\'t have permissions to do this']
                ], 403);
            }
        }
    }
}
