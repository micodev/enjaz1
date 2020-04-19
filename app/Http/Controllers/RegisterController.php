<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\User;

class registerController extends Controller
{
    //

    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required | unique:users',
            'password' => 'required | min:6',
            'c_password' => 'required | same:password',
            'role_id' => 'required',
            'company_id' => 'required'
        ]);
        if($validator->fails())
        return response()->json([
            'errors' => $validator->errors()
        ]);
        
        User::create([
            'name' => $request['name'],
            'username' => $request['username'],
            'password' => Hash::make($request['password']),
            'role_id' => $request['role_id'],
            'company_id' => $request['company_id'],
        ]);
        
        return response()->json([
            'response' => 'done'
        ]);
    }
}
