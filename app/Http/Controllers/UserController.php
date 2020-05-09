<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Token;
use Illuminate\Support\Str;




class UserController extends Controller
{

    public function login(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);

        if (Auth::attempt(['username' => $request['username'], 'password' => $request['password']])) {

            $user = User::where('username', $request['username'])->with(['role', 'company'])->first();
            if ($user->active) {
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
            } else
                return response()->json([
                    'response' => 1
                ]);
        } else  return response()->json([
            'response' => 3
        ]);
    }

    public function register(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'name' => 'required',
            'username' => 'required | unique:users',
            'password' => 'required | min:6',
            'role_id' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator->fails())
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

    public function delete(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'id' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $done = User::where('id', $request['id'])->first()->delete();
        if ($done)
            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'response' => 2
            ]);
    }
    public function update(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'id' => 'required',
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $rules = [
            'name' => 'required',
            'username' => 'required | unique:users,username,' . $request['id'],
            'role_id' => 'required',
            'company_id' => 'required',
            'active' => 'required'
        ];
        if ($request['password'] != null)
            $rules['password'] =  'min:6';
        $validator = Validator::make($request, $rules);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $fields = [
            'name' => $request['name'],
            'username' => $request['username'],
            'role_id' => $request['role_id'],
            'company_id' => $request['company_id'],
            'active' => $request['active']
        ];
        if ($request['password'] != null) {
            $fields['password'] = Hash::make($request['password']);
        }


        $done = User::where('id', $request['id'])->first()->update($fields);
        if ($done)
            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'response' => 2
            ]);
    }
    public function show()
    {

        $users =  User::with(['company', 'role'])->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'response' => $users
        ]);
    }
    public function search(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $empty = true;
        $users = User::with(['company', 'role'])->orderBy('created_at', 'desc');
        if ($request['username'] != null) {
            $users = $users->where('username', $request['username']);
            $empty = false;
        }
        if ($request['name'] != null) {
            $users = $users->where('name', 'like', '%' . $request['name'] . '%');
            $empty = false;
        }
        if ($request['role_id'] != null) {
            $users = $users->where('role_id', $request['role_id']);
            $empty = false;
        }
        if ($request['company_id'] != null) {
            $users = $users->where('company_id', $request['company_id']);
            $empty = false;
        }
        if ($empty)
            return response()->json([
                'response' => 4
            ]);
        $users = $users->paginate(20);
        return response()->json([
            'response' => $users
        ]);
    }
}
