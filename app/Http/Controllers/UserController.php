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
                'response' => 5
            ], 400);

        if (Auth::attempt(['username' => $request['username'], 'password' => $request['password']])) {

            $user = User::where('username', $request['username'])->with(['role', 'company'])->first();
            if ($user->active) {
                $data = array(
                    'api_token' => Str::random(50),
                    'user_id' => $user->id,
                );

                if (isset($request['notify_token']))
                    $data['notify_token'] = $request['notify_token'];
                $token = Token::create($data);
                return response()->json([
                    'response' => [
                        'user' => $user,
                        'token' => $token->api_token
                    ]
                ]);
            } else
                return response()->json([
                    'response' => 1
                ], 403);
        } else  return response()->json(
            [
                'response' => 3
            ],
            401
        );
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
                'response' => 5
            ], 400);
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
                'response' => 5
            ], 400);
        $done = User::where('id', $request['id'])->first()->delete();
        if ($done)
            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'response' => 2
            ], 422);
    }
    public function update(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'id' => 'required',
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
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
                'response' => 5
            ], 400);
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
            ], 422);
    }
    public function show($id = null)
    {
        // return $request->query('company', 'default');
        if ($id != null)
            $users =  User::with(['company', 'role'])->orderBy('created_at', 'desc')
                ->where('company_id', $id)->paginate(20);
        else
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
            ], 406);
        $users = $users->paginate(20);
        return response()->json([
            'response' => $users
        ]);
    }

    public function logout(Request $request)
    {
        if (!$request->bearerToken())
            return response()->json([
                'response' => 3
            ], 401);


        Token::where('api_token', $request->bearerToken())->first() ?
            Token::where('api_token', $request->bearerToken())->first()->delete() : null;
        return response()->json([
            'response' => 'done'
        ]);
    }
}
