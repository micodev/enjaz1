<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Token;
use Illuminate\Support\Str;
use App\User;


class LoginController extends Controller
{
    //

    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails())
        return response()->json([
            'errors' => $validator->errors()
        ]);

        if (Auth::attempt(['username' => $request['username'], 'password' => $request['password']])){
            $user = auth()->user()->with(['role', 'company'])->first();
            $token = Token::create([
                'api_token' => Str::random(50),
                'user_id' => $user->id,
            ]);
            return response()->json([
                'response' => [
                    'user' => $user,
                    'token' => $token->api_token
                ]
            ]);
        }else  return response()->json([
            'response' => 'unauthorized'
        ]);
    }
    
}
