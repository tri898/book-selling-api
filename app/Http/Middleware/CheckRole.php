<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
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
        if (auth()->user()->role == 1) {
            return $next($request);
        }
        $response = [
            'success' => false,
            'message' => 'Deny access',
        ];
        return response()->json($response, 400);
    }
}
