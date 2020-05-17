<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Token;
use App\Note;
use Validator;
use File;
use DateTime;
use Illuminate\Support\Str;

class noteController extends Controller
{
    private function getUser($token)
    {
        return Token::where('api_token', $token)->first()->user()->first();
    }
    public function create(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'doc_date' => 'required',
            'note' => 'required',
            'company_id' => 'required | integer',
            'doc_number' => 'required | integer',
            // 'doc_number' => 'required | unique:notes',
            'outcoming' => 'required',
            'incoming' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $images = [];
        if (isset($request['images'])) {
            if (!file_exists(public_path() . '/images/note')) {
                File::makeDirectory(public_path() . '/images/note');
            }
            $names = [];
            foreach ($request['images'] as $image) {


                $image = explode(',', $image)[1];

                $imgdata = base64_decode($image);

                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);

                $type = explode('/', $mime_type)[1];


                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/note/' . $filename, $imgdata);

                array_push($names, '/images/note/' . $filename);
            }
            $images = $names;
        }


        Note::create([
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

        return response()->json([
            'response' => 'done'
        ]);
    }

    public function showNotes()
    {
        $notes = Note::with(['company', 'user'])
            ->where('deleted', false)
            ->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $notes
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
        $note = Note::where('id', $id)->first();
        if (!$note)
            return response()->json([
                'response' => 2
            ], 422);
        $done = $note->update([
            'deleted' => true
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


    public function deleteImage(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'note_id' => 'required',
            'img_path' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
        $id = $request['note_id'];
        $note = Note::where('id', $id)->first();
        if (!$note)
            return response()->json([
                'response' => 2
            ], 422);
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



    public function search(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $empty = true;

        $notes = Note::with(['company', 'user'])
            ->where('deleted', false)
            ->orderBy('created_at', 'desc');

        if (isset($request['company_id'])) {
            $notes = $notes->where('company_id', $request['company_id']);
            $empty = false;
        }
        if (isset($request['title'])) {
            $notes = $notes->where('title', 'like', '%' . $request['title'] . '%');
            $empty = false;
        }
        if (isset($request['incoming'])) {
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
            $date = new DateTime($request['date_from']);
            $date->modify('-1 day');
            $from = $date->format('Y-m-d');
            $to = $request['date_to'];
            $notes = $notes->whereBetween('doc_date', [$from . '%', $to . '%']);
        }

        if ($empty)
            return response()->json([
                'response' => 4
            ], 400);


        $notes = $notes->paginate(5);
        return response()->json([
            'response' => $notes
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        // $user = User::where('id', '1')->first();
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];


        $validator = Validator::make($request, [
            'id' => 'required',
            'title' => 'required',
            'doc_date' => 'required',
            'doc_number' => 'required',
            'note' => 'required',
            'incoming' => 'required',
            'outcoming' => 'required',
            'company_id' => 'required | integer'
        ]);
        //  return $request;
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
        $note = Note::where('id', $request['id'])->first();
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
                File::put(public_path() . '/images/note/' . $filename, $imgdata);

                array_push($names, '/images/note/' . $filename);
            }
            $new_images = $names;
        }
        $images = array_merge($note->images, $new_images);
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

        $done =  $note->update($data);

        if ($done)
            return response()->json([
                'response' => $note
            ]);
        else
            return response()->json([
                'response' => 2
            ], 422);
    }
}
