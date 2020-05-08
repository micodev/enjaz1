<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;

class IsActive
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
        $active = Token::where('api_token', $request->bearerToken())->first() ?
        Token::where('api_token', $request->bearerToken())->first()->user()->first()->active : 0;
        if ($active)
            return $next($request);
        return response()->json([
            'error' => 1
        ]);
    }
}
