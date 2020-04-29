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

class contractController extends Controller
{
    private function getUser($token)
    {
        return Token::where('api_token', $token)->first()->user()->first();
    }
    public function create(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        // $user = User::where('id', '1')->first();
        $request = json_decode($request->getContent(), true);


        $validator = Validator::make($request, [
            'type' => 'required',
            'doc_date' => 'required',
            'note' => 'required',
            'company' => 'required',
            'doc_number' => 'required',
            'destination' => 'required',
            'action' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        
        $images = '';
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

        Contract::create([
            'type_id' => $request['type'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'company_id' => $request['company'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => '3',
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action'],
            'title' => $request['title']

        ]);

        return response()->json([
            'response' => 'done'
        ]);
    }
    public function showContracts()
    {
        $contracts = Contract::with(['company', 'type', 'state', 'user', 'action'])->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $contracts
        ]);
    }

    public function delete(Request $request)
    {
        $request = json_decode($request->getContent(), true);

        $id = $request['id'];
        $contract = Contract::where('id', $id)->first()->delete();
        return response()->json([
            'response' => 'done'
        ]);
    }

    public function deleteImage(Request $request)
    {
        $request = json_decode($request->getContent(), true);

        $id = $request['contract_id'];

        $contract = Contract::where('id', $id)->first();
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
        $request = json_decode($request->getContent(), true);
        $empty = true;

        $contracts = Contract::with(['company', 'type', 'state', 'user', 'action'])->orderBy('created_at', 'desc');
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
                'response' => 'Bad Request'
            ]);
        $contracts = $contracts->paginate(5);
        return response()->json([
            'response' => $contracts
        ]);
    }
    public function update(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        // $user = User::where('id', '1')->first();
        $request = json_decode($request->getContent(), true);

        $validator = Validator::make($request, [
            'type_id' => 'required',
            'doc_date' => 'required',
            'note' => 'required',
            'company_id' => 'required',
            'doc_number' => 'required',
            'destination' => 'required',
            'action_id' => 'required',
            'title' => 'required',

        ]);

        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
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
            'note' => $request['note'],
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => '1',
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title']


        );

        $contract->update($data);
        return response()->json([
            'response' => $contract
        ]);
    }
    public function waitContracts()
    {
        $contracts = Contract::with(['company', 'type', 'state', 'user', 'action'])->where('state_id', '3')
            ->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $contracts
        ]);
    }
    public function changeState(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        $contract = Contract::where('id', $request['id'])->first();
        $contract->state_id = $request['state_id'];
        $contract->save();
        return response()->json([
            'response' => 'done'
        ]);
    }
}
