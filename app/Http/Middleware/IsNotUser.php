<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;

class IsNotUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $role = Token::where('api_token', $request->bearerToken())->first() ?
            Token::where('api_token', $request->bearerToken())->first()->user()->first()->role_id : 3;
        if ($role == 1 || $role == 2)
            return $next($request);
        return response()->json([
            'response' => 1
        ], 401);
    }
}
