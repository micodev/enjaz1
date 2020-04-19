<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Token;

class CheckApiToken
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
        if(!empty($request->bearerToken())){
            $token = Token::where('api_token', $request->bearerToken())->first();
           // return response()->json($user);
            if ($token)
            return $next($request);
            else return response()->json('Invalid Token', 401);
        }else  return response()->json('Bad Request', 401);
      //  return $next($request);
    }
}
