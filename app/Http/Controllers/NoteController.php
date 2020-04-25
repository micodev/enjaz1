<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Token;
use App\Note;
use Validator;
use File;

class noteController extends Controller
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

            'doc_date' => 'required',
            'note' => 'required',
            'company_id' => 'required',
            'doc_number' => 'required',
            'outcoming' => 'required',
            'incoming' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        //  dd($request->image);
        $images = [];
        if (isset($request['images'])) {
            $names = [];
            foreach ($request['images'] as $image) {


                // $Path = public_path() . '/images/note/';
                // $filename = time() . $image->getClientOriginalName();
                // // $ex = $image->getClientOriginalExtension();
                // $image->move($Path, $filename);

                // array_push($names, '/images/note/' . $filename);

                $image = explode(',', $image)[1];
       
                $imgdata = base64_decode($image);
               
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                //return $mime_type;
                $type = explode('/', $mime_type)[1];
        
                //  $image = str_replace(' ', '+', $image);
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/note/' . $filename, $imgdata);

                array_push($names, '/images/note/' . $filename);
            }
            $images = $names;
        }
        // return $images;

        $n =  Note::create([
            'title' => $request['title'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'incoming' => $request['incoming'],
            'outcoming' => $request['outcoming'],
            'doc_number' => $request['doc_number'],

        ]);
        // return json_decode($n->images);
        return response()->json([
            'response' => 'done'
        ]);
    }

    public function showNotes()
    {
        $notes = Note::with(['company', 'user'])->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $notes
        ]);
    }

    public function delete(Request $request)
    {
        $request =json_decode($request->getContent(), true);

        $id = $request['id'];
        $note = Note::where('id', $id)->first()->delete();
        return response()->json([
            'response' => 'done'
        ]);
    }

    // public function searchNote(Request $request)
    // {
    //     if (isset($request['title']))
    //         $notes = Note::where('title', 'like', '%' . $request['title'] . '%');
    //     if (isset($request['incoming']))
    //         $notes = $notes->where('incoming', 'like', '%' . $request['incoming'] . '%');
    //     if (isset($request['outcoming']))
    //     $notes = $notes->where('outcoming', 'like', '%' . $request['outcoming'] . '%');
    //     if (isset($request['from']) && isset($request['to'])) {
    //         // $from =  Carbon::parse($request['from'])
    //         //     ->startOfDay()        // 2018-09-29 00:00:00.000000
    //         //     ->toDateTimeString(); // 2018-09-29 00:00:00;
    //         // $to =  Carbon::parse($request['to'])
    //         //     ->startOfDay()        // 2018-09-29 00:00:00.000000
    //         //     ->toDateTimeString(); // 2018-09-29 00:00:00;

    //         $from = $request['from'];
    //         $to = $request['to'];

    //         $notes = $notes->whereBetween('created_at', [$from . '%', $to . '%']);
    //     }
    //     $notes = $notes->paginate(1);
    //     return response()->json([
    //         'response' => $notes
    //     ]);
    // }

    public function deleteImage(Request $request)
    {
        $request =json_decode($request->getContent(), true);

        $id = $request['note_id'];
        $note = Note::where('id', $id)->first();
        $images = $note->images;

        $path = $request['img_path'];
        $images =   array_diff($images, [$path]);
        $note->images = $images;
        $note->save();
        if (File::exists($path)) {
            File::delete($path);
        }
        return response()->json([
            'response' => 'done'
        ]);
    }

    public function searchNote(Request $request) //mobile search
    {
        $notes = Note::with(['company', 'user'])->orderBy('created_at', 'desc');
        if (isset($request['title']))
            $notes = $notes->where('title', 'like', '%' . $request['title'] . '%');
        if (isset($request['incoming']))
            $notes = $notes->where('incoming', 'like', '%' . $request['incoming'] . '%');
        if (isset($request['outcoming']))
            $notes = $notes->where('outcoming', 'like', '%' . $request['outcoming'] . '%');
        if (isset($request['company_id']))
            $notes = $notes->where('company_id', $request['company_id']);
        if (isset($request['date_from']) && isset($request['date_to'])) {

            $from = $request['date_from'];
            $to = $request['date_to'];

            $notes = $notes->whereBetween('doc_date', [$from . '%', $to . '%']);
        }
        $notes = $notes->paginate(1);
        return response()->json([
            'response' => $notes
        ]);
    }

    public function search(Request $request)
    {
        $request =json_decode($request->getContent(), true);


        // $title = $request['title'];
        // $doc_number = $request['doc_number'];
        // $company_id = $request['company_id'];
        // $incoming = $request['incoming'];
        // $outcoming = $request['outcoming'];
        $empty = true;
        $notes = Note::with(['company', 'user'])->orderBy('created_at', 'desc');
        if (isset($request['company_id'])) {
            $notes = $notes->where('company_id', $request['company_id']);
            $empty = false;
        }
        if (isset($request['title'])) {
            $notes = $notes->where('title', 'like', '%' . $request['title'] . '%');
            $empty = false;
        }
        if ($request['incoming']) {
            $notes = $notes->where('incoming', 'like', '%' . $request['incoming'] . '%');
            $empty = false;
        }
        if (isset($request['outcoming'])) {
            $notes = $notes->where('outcoming', 'like', '%' . $request['outcoming'] . '%');
            $empty = false;
        }
        if (isset($request['doc_number'])) {
            $notes = $notes->where('doc_number', $request['doc_number']);
            $empty = false;
        }

        if (isset($request['date_from']) && isset($request['date_to'])) {
            $empty = false;
            $from = $request['date_from'];
            $to = $request['date_to'];

            $notes = $notes->whereBetween('doc_date', [$from . '%', $to . '%']);
        }

        if ($empty)
            return response()->json([
                'response' => 'Bad Request'
            ]);
        $notes = $notes->paginate(5);
        return response()->json([
            'response' => $notes
        ]);
    }

    public function update(Request $request)
    {
      $user = $this->getUser($request->bearerToken());
       // $user = User::where('id', '1')->first();
       $request =json_decode($request->getContent(), true);

        $validator = Validator::make($request, [
            'id' => 'required',
            'title' => 'required',
            'doc_date' => 'required',
            'doc_number' => 'required',
            'note' => 'required',
            'incoming' => 'required',
            'outcoming' => 'required',
            'company_id' => 'required'
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


                // $Path = public_path() . '/images/note/';
                // $filename = time() . $image->getClientOriginalName();
                // $ex = $image->getClientOriginalExtension();
                // $image->move($Path, $filename);

                // array_push($names, '/images/note/' . $filename);

                $image = explode(',', $image)[1];
       
                $imgdata = base64_decode($image);
               
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                //return $mime_type;
                $type = explode('/', $mime_type)[1];
        
                //  $image = str_replace(' ', '+', $image);
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/note/' . $filename, $imgdata);

                array_push($names, '/images/note/' . $filename);
            }
            $images = $names;
        }

        $data = array(
            'title' => $request['title'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'images' => $images,
            'doc_number' => $request['doc_number'],
            'incoming' => $request['incoming'],
            'outcoming' => $request['outcoming'],
            'company_id' => $request['company_id'],
            'user_id' => $user->id

        );

        Note::where('id', $request['id'])->update($data);
        return response()->json([
            'response' => 'done'
        ]);
    }
}
