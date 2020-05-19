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

        $token = $request->bearerToken() ? $request->bearerToken() : null;
        if (!$token)
            return response()->json([
                'response' => 3
            ], 401);
        $active = Token::where('api_token', $request->bearerToken())->first() ?
            Token::where('api_token', $request->bearerToken())->first()->user()->first()->active : null;
        if (is_null($active))
            return response()->json([
                'response' => 3
            ], 401);
        if ($active)
            return $next($request);
        else
            return response()->json([
                'response' => 1
            ], 403);
    }
}
