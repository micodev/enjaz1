<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Token;
use App\Contract;
use Validator;
use File;
use DateTime;

class contractController extends Controller
{
    private function getUser($token)
    {
        $id = Token::where('api_token', $token)->first()->user_id;
        return User::where('id', $id)->first();
    }
    public function create(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
       // $user = User::where('id', '1')->first();
       $request =json_decode($request->getContent(), true);


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
        //  dd($request->image);
        $images = '';
        if (isset($request['images'])) {
            $names = [];
            foreach ($request['images'] as $image) {


                // $Path = public_path() . '/images/contract/';
                // $filename = time() . $image->getClientOriginalName();
                // $ex = $image->getClientOriginalExtension();
                // $image->move($Path, $filename);

                // array_push($names, '/images/contract/' . $filename);

                $image = explode(',', $image)[1];
       
                $imgdata = base64_decode($image);
               
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                //return $mime_type;
                $type = explode('/', $mime_type)[1];
        
                //  $image = str_replace(' ', '+', $image);
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/contract/' . $filename, $imgdata);

                array_push($names, '/images/contract/' . $filename);
            }
            $images = $names;
        }
        // return $images;

        $b =  Contract::create([
            'type_id' => $request['type'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'company_id' => $request['company'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => '4',
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action'],
            'title' => $request['title']

        ]);
       // return $b->images;
        return response()->json([
            'response' => 'done'
        ]);
    }
    public function showContracts()
    {
        $contracts =Contract::with(['company', 'type', 'state', 'user', 'action'])->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $contracts
        ]);
    }

    public function delete(Request $request)
    {
        $request =json_decode($request->getContent(), true);

        $id = $request['id'];
        $contract = Contract::where('id', $id)->first()->delete();
        return response()->json([
            'response' => 'done'
        ]);
    }
    // public function searchContract(Request $request)
    // {
    //     if (isset($request['title']))
    //         $contracts = Contract::where('title', 'like', '%' . $request['title'] . '%');
    //     if (isset($request['destination']))
    //         $contracts = $contracts->where('destination', 'like', '%' . $request['destination'] . '%');
    //     if (isset($request['action_id']))
    //         $contracts = $contracts->where('action_id', $request['action_id']);
    //     if (isset($request['type_id']))
    //         $contracts = $contracts->where('type_id', $request['type_id']);
    //     if (isset($request['company_id']))
    //         $contracts = $contracts->where('company_id', $request['company_id']);
    //     if (isset($request['from']) && isset($request['to'])) {
    //         // $from =  Carbon::parse($request['from'])
    //         //     ->startOfDay()        // 2018-09-29 00:00:00.000000
    //         //     ->toDateTimeString(); // 2018-09-29 00:00:00;
    //         // $to =  Carbon::parse($request['to'])
    //         //     ->startOfDay()        // 2018-09-29 00:00:00.000000
    //         //     ->toDateTimeString(); // 2018-09-29 00:00:00;

    //         $from = $request['from'];
    //         $to = $request['to'];

    //         $contracts = $contracts->whereBetween('created_at', [$from . '%', $to . '%']);
    //     }
    //     $contracts = $contracts->paginate(1);
    //     return response()->json([
    //         'response' => $contracts
    //     ]);
    // }
    public function deleteImage(Request $request)
    {
        $request =json_decode($request->getContent(), true);

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

    // public function search(Request $request)
    // {
    //     $title = $request['title'];
    //     $company_id = $request['company_id'];
    //     $state_id = $request['state_id'];
    //     $type_id = $request['type_id'];
    //     $doc_number = $request['doc_number'];
    //     $action_id = $request['action_id'];
    //     //check for user
    //     $contracts = Contract::where('company_id', $company_id);
    //     if ($title != null)
    //         $contracts = $contracts->where('title','like', '%'. $title .'%');

    //     if (isset($request['from']) && isset($request['to'])) {

    //         $from = $request['from'];
    //         $to = $request['to'];

    //         $contracts = $contracts->whereBetween('created_at', [$from . '%', $to . '%']);
    //     }
    //     $contracts = $contracts->paginate(1);

    //     return response()->json([
    //         'response' => $contracts
    //     ]);
    // }
    public function searchContract(Request $request)
    {
        $contracts = Contract::with(['company', 'type', 'state', 'user', 'action'])->orderBy('created_at', 'desc');
        if (isset($request['title']))
            $contracts = $contracts->where('title', 'like', '%' . $request['title'] . '%');
        if (isset($request['destination']))
            $contracts = $contracts->where('destination', 'like', '%' . $request['destination'] . '%');
        if (isset($request['action_id']))
            $contracts = $contracts->where('action_id', $request['action_id']);
        if (isset($request['type_id']))
            $contracts = $contracts->where('type_id', $request['type_id']);
        if (isset($request['company_id']))
            $contracts = $contracts->where('company_id', $request['company_id']);
        if (isset($request['date_from']) && isset($request['date_to'])) {

            $date = new DateTime($request['date_from']);
            $date->modify('-1 day');
            $from = $date->format('Y-m-d');
            $to = $request['date_to'];

            $contracts = $contracts->whereBetween('doc_date', [$from . '%', $to . '%']);
        }
        $contracts = $contracts->paginate(1);
        return response()->json([
            'response' => $contracts
        ]);
    }

    public function search(Request $request)
    {
        $request =json_decode($request->getContent(), true);

        // $title = $request['title'];
        // $destination = $request['destination'];
        // $doc_number = $request['doc_number'];
        // $type = $request['type_id'];
        // $state = $request['state_id'];
        // $company_id = $request['company_id'];
        // $action = $request['action_id'];
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

            $from = $request['date_from'];
            $to = $request['date_to'];

            $contracts = $contracts->whereBetween('doc_date', [$from . '%', $to . '%']);
        }

        if ($empty)
            return response()->json([
                'response' => 'Bad Request'
            ]);
        $contracts = $contracts->paginate(1);
        return response()->json([
            'response' => $contracts
        ]);
    }
    public function update(Request $request)
    {
          $user = $this->getUser($request->bearerToken());
       // $user = User::where('id', '1')->first();
        $request =json_decode($request->getContent(), true);

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
        //  return $request;
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        //  dd($request->image);
        $images = [];
        if (isset($request['images'])) {
            $names = [];
            foreach ($request['images'] as $image) {


                // $Path = public_path() . '/images/contract/';
                // $filename = time() . $image->getClientOriginalName();
                // $ex = $image->getClientOriginalExtension();
                // $image->move($Path, $filename);

                // array_push($names, '/images/contract/' . $filename);

                $image = explode(',', $image)[1];
       
                $imgdata = base64_decode($image);
               
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                //return $mime_type;
                $type = explode('/', $mime_type)[1];
        
                //  $image = str_replace(' ', '+', $image);
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/contract/' . $filename, $imgdata);

                array_push($names, '/images/contract/' . $filename);
            }
            $images = $names;
        }

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

        Contract::where('id', $request['id'])->update($data);
        return response()->json([
            'response' => 'done'
        ]);
    }
}
