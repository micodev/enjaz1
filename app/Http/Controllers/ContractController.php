<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Token;
use App\Contract;
use Validator;
use File;
use DateTime;
use Illuminate\Support\Str;
use App\Http\Traits\Fcm;
use App\Notify;
use App\Type;

class contractController extends Controller
{
    use Fcm;
    private function getUser($token)
    {
        return Token::where('api_token', $token)->first()->user()->first();
    }
    public function create(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'type_id' => 'required | integer',
            'doc_date' => 'required',
            'company_id' => 'required | integer',
            'doc_number' => 'required',
            // 'doc_number' => 'required | unique:contracts',
            'destination' => 'required',
            'action_id' => 'required | integer',
            'title' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $type = Type::where('id', $request['type_id'])->first();
        if ($type->table)
            return response()->json([
                'response' => 6
            ], 405);

        $images = [];
        if (isset($request['images'])) {
            if (!file_exists(public_path() . '/images/contract')) {
                File::makeDirectory(public_path() . '/images/contract');
            }
            $names = [];
            foreach ($request['images'] as $image) {

                $image = explode(',', $image)[1];

                $imgdata = base64_decode($image);

                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $type = explode('/', $mime_type)[1];
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/contract/' . $filename, $imgdata);

                array_push($names, '/images/contract/' . $filename);
            }
            $images = $names;
        }

        $contract =   Contract::create([
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => $user->role_id != 3 ? 1 : 3,
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title'],
            'body' => isset($request['body']) ? $request['body'] : ""

        ]);
        if ($user->role_id == 3) {
            Notify::create([
                'contract_id' => $contract->id,
                'user_id' => $user->id,
                'type' => false,
                'role_id' => 2,
                'notify_type' => false

            ]);

            $users = User::with(['tokens' => function ($q) {
                $q->where('notify_token', '!=', null);
            }])->where('role_id', 2)->get();
            foreach ($users as $user) {
                if (count($user->tokens) > 0) {
                    foreach ($user->tokens as $tokens) {
                        $this->NotifySuper($tokens->notify_token, $request['title']);
                    }
                }
            }
        }

        return response()->json([
            'response' => 'done'
        ]);
    }
    public function createContract(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];


        $validator = Validator::make($request, [
            'type_id' => 'required | integer',
            'doc_date' => 'required',
            'company_id' => 'required | integer',
            'doc_number' => 'required',
            // 'doc_number' => 'required | unique:contracts',
            'destination' => 'required',
            'action_id' => 'required | integer',
            'title' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $type = Type::where('id', $request['type_id'])->first();
        if ($type->table)
            return response()->json([
                'response' => 6
            ], 405);

        $images = [];
        if (isset($request['images'])) {
            if (!file_exists(public_path() . '/images/contract')) {
                File::makeDirectory(public_path() . '/images/contract');
            }
            $names = [];
            foreach ($request['images'] as $image) {

                $image = explode(',', $image)[1];

                $imgdata = base64_decode($image);

                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $type = explode('/', $mime_type)[1];
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/contract/' . $filename, $imgdata);

                array_push($names, '/images/contract/' . $filename);
            }
            $images = $names;
        }
        $contract =   Contract::create([
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => $user->role_id == 1 ? 1 : 3,
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title'],
            'body' => isset($request['body']) ? $request['body'] : ""

        ]);

        if ($user->role_id != 1) {
            Notify::create([
                'contract_id' => $contract->id,
                'user_id' => $user->id,
                'type' => true,
                'role_id' => 1,
                'notify_type' => false

            ]);

            $users = User::with(['tokens' => function ($q) {
                $q->where('notify_token', '!=', null);
            }])->where('role_id', 1)->get();
            foreach ($users as $user) {
                if (count($user->tokens) > 0) {
                    foreach ($user->tokens as $tokens) {
                        $this->NotifyAdmin($tokens->notify_token, $request['title']);
                    }
                }
            }
        }
        return response()->json([
            'response' => 'done'
        ]);
    }
    public function showContracts()
    {
        $contracts = Contract::with(['company', 'type', 'state', 'user', 'action'])
            ->where('deleted', false)
            ->where('state_id',  1)
            ->orderBy('created_at', 'desc')->paginate(15);
        return response()->json([
            'response' => $contracts
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

        $id = $request['id'];
        $contract = Contract::where('id', $id)->first();

        if (!$contract)
            return response()->json([
                'response' => 2
            ], 422);

        $done = $contract->update([
            'deleted' => true
        ]);
        if ($done)
            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'response' =>  2
            ], 422);
    }

    public function deleteImage(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'contract_id' => 'required',
            'img_path' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $id = $request['contract_id'];

        $contract = Contract::where('id', $id)->first();
        if (!$contract)
            return response()->json([
                'response' => 2
            ], 422);
        $images = $contract->images;

        $path = $request['img_path'];
        $images =   array_diff($images, [$path]);
        $contract->images = $images;
        $contract->save();
        if (File::exists($path)) {
            File::delete($path);
        }
        return response()->json([
            'response' => 'done'
        ]);
    }



    public function search(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $empty = true;

        $contracts = Contract::with(['company', 'type', 'state', 'user', 'action'])
            ->where('deleted', false)
            ->where('state_id', 1);
       

        $contracts = $contracts->orderBy('created_at', 'desc');

        if (isset($request['company_id'])) {

            $contracts = $contracts->where('company_id', $request['company_id']);
            $empty = false;
        }

        if (isset($request['title'])) {
            $contracts = $contracts->where('title', 'like', '%' . $request['title'] . '%');
            $empty = false;
        }

        if (isset($request['destination'])) {
            $contracts = $contracts->where('destination', 'like', '%' . $request['destination'] . '%');
            $empty = false;
        }


        if (isset($request['type_id'])) {
            $contracts = $contracts->where('type_id', $request['type_id']);
            $empty = false;
        }

        if (isset($request['state_id'])) {
            $contracts = $contracts->where('state_id', $request['state_id']);
            $empty = false;
        }

        if (isset($request['action_id'])) {
            $contracts = $contracts->where('action_id', $request['action_id']);
            $empty = false;
        }
        if (isset($request['doc_number'])) {
            $contracts = $contracts->where('doc_number', $request['doc_number']);
            $empty = false;
        }


        if (isset($request['date_from']) && isset($request['date_to'])) {
            $empty = false;

            $date = new DateTime($request['date_from']);
            $date->modify('-1 day');
            $from = $date->format('Y-m-d');
            $to = $request['date_to'];

            $contracts = $contracts->whereBetween('doc_date', [$from . '%', $to . '%']);
        }

        if ($empty)
            return response()->json([
                'response' => 4
            ], 406);
        $contracts = $contracts->paginate(15);
        return response()->json([
            'response' => $contracts
        ]);
    }
    public function update(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];


        $validator = Validator::make($request, [
            'id' => 'required',
            'type_id' => 'required | integer',
            'doc_date' => 'required',
            'company_id' => 'required | integer',
            'doc_number' => 'required',
            'destination' => 'required',
            'action_id' => 'required | integer',
            'title' => 'required',
            'state_id' => 'required | integer'

        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
        $contract = Contract::where('id', $request['id'])->first();
        $new_images = [];
        if ($request['temp'] != null) {
            $names = [];
            foreach ($request['temp'] as $image) {

                $image = explode(',', $image)[1];

                $imgdata = base64_decode($image);

                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $type = explode('/', $mime_type)[1];
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/contract/' . $filename, $imgdata);

                array_push($names, '/images/contract/' . $filename);
            }
            $new_images = $names;
        }
        $images = array_merge($contract->images, $new_images);
        $data = array(
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => $request['state_id'],
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title']


        );
        if ($user->role_id == 1)
            $data['body'] = $request['body'];

        $done =  $contract->update($data);
        if ($done)
            return response()->json([
                'response' =>  $contract
            ]);
        else
            return response()->json([
                'response' =>  2
            ], 422);
    }
    public function waitContracts()
    {
        $contracts = Contract::with(['company', 'type', 'state', 'user', 'action'])->where('state_id', 3)
            ->orderBy('created_at', 'desc')->paginate(15);
        return response()->json([
            'response' => $contracts
        ]);
    }
    public function changeState(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'id' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
        $done = Contract::where('id', $request['id'])->first()->update([
            'state_id' => $request['state_id']
        ]);
        if ($done)
            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'response' => 2
            ], 422);
    }

    public function edit(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'contract' => 'required',
            'user' => 'required'

        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $validator = Validator::make($request['contract'], [
            'type_id' => 'required | integer',
            'doc_date' => 'required',
            'company_id' => 'required | integer',
            'doc_number' => 'required',
            // 'doc_number' => 'required | unique:contracts',
            'destination' => 'required',
            'action_id' => 'required | integer',
            'title' => 'required',

        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
        $contract = Contract::where('id', $request['contract']['id'])->first();
        $new_images = [];
        if ($request['contract']['temp'] != null) {
            $names = [];
            foreach ($request['contract']['temp'] as $image) {

                $image = explode(',', $image)[1];

                $imgdata = base64_decode($image);

                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $type = explode('/', $mime_type)[1];
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/contract/' . $filename, $imgdata);

                array_push($names, '/images/contract/' . $filename);
            }
            $new_images = $names;
        }
        $images = array_merge($contract->images, $new_images);
        $request['contract']['images'] = $new_images;

        $notify = Notify::where('id', $request['id'])->first();
        if ($notify->seen)
            return response()->json([
                'response' => 2
            ], 422);
        $notify->update(['seen' => true]);
        if (!$notify->notify_type && $notify->type) {
            Notify::create([
                'contract_id' => $notify->contract_id,
                'user_id' => $notify->user_id,
                'type' => $notify->type,
                'role_id' => $notify->role_id,
                'notify_type' => true

            ]);

            $user = User::with(['tokens' => function ($q) {
                $q->where('notify_token', '!=', null);
            }])->where('id', $request['user']['id'])->first();

            if (count($user->tokens) > 0) {
                foreach ($user->tokens as $tokens) {
                    $this->NotifyState($tokens->notify_token, $request['contract']['title'], $request['contract']['state_id'] == 1 ? true : false);
                }
            }
        }
        $done =  $contract->update($request['contract']);
        if ($done)
            return response()->json([
                'response' =>  $contract
            ]);
        else
            return response()->json([
                'response' =>  2
            ], 422);
    }
}
