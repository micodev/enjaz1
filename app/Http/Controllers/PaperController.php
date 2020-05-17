<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Token;
use App\Paper;
use Illuminate\Support\Str;
use File;
use DateTime;



class paperController extends Controller
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
            'title' => 'required',
            'doc_date' => 'required',
            'company_id' => 'required | integer'
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
        $images = [];
        if (isset($request['images'])) {
            if (!file_exists(public_path() . '/images/paper')) {
                File::makeDirectory(public_path() . '/images/paper');
            }
            $names = [];
            foreach ($request['images'] as $image) {

                $image = explode(',', $image)[1];

                $imgdata = base64_decode($image);

                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $type = explode('/', $mime_type)[1];
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/paper/' . $filename, $imgdata);
                array_push($names, '/images/paper/' . $filename);
            }
            $images = $names;
        }


        Paper::create([
            'title' => $request['title'],
            'doc_date' => $request['doc_date'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images

        ]);

        return response()->json([
            'response' => 'done'
        ]);
    }

    public function showPapers()
    {
        $papers = Paper::with(['company', 'user'])
            ->where('deleted', false)
            ->orderBy('created_at', 'desc')->paginate(5);

        return response()->json([
            'response' => $papers
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
        $paper = Paper::where('id', $id)->first();
        if (!$paper)
            return response()->json([
                'response' => 2
            ], 422);

        $done = $paper->update([
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

    public function search(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];


        $papers = Paper::with(['company', 'user'])
            ->where('deleted', false)
            ->orderBy('created_at', 'desc');
        $empty = true;
        if (isset($request['company_id'])) {
            $papers = $papers->where('company_id', $request['company_id']);
            $empty = false;
        }
        if (isset($request['title'])) {
            $papers = $papers->where('title', 'like', '%' . $request['title'] . '%');
            $empty = false;
        }
        if (isset($request['date_from']) && isset($request['date_to'])) {

            $date = new DateTime($request['date_from']);
            $date->modify('-1 day');
            $from = $date->format('Y-m-d');
            $to = $request['date_to'];

            $papers = $papers->whereBetween('doc_date', [$from . '%', $to . '%']);
            $empty = false;
        }
        if ($empty)
            return response()->json([
                'response' => 4
            ], 400);

        $papers = $papers->paginate(5);

        return response()->json([
            'response' => $papers
        ]);
    }



    public function deleteImage(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'paper_id' => 'required',
            'img_path' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $id = $request['paper_id'];
        $paper = Paper::where('id', $id)->first();
        if (!$paper)
            return response()->json([
                'response' => 2
            ], 422);
        $images = $paper->images;

        $path = $request['img_path'];
        $images =   array_diff($images, [$path]);
        $paper->images = $images;
        $paper->save();
        if (File::exists($path)) {
            File::delete($path);
        }
        return response()->json([
            'response' => 'done'
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];


        $validator = Validator::make($request, [
            'id' => 'required',
            'title' => 'required',
            'doc_date' => 'required',
            'company_id' => 'required | integer'
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);
        $paper = Paper::where('id', $request['id'])->first();
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
                File::put(public_path() . '/images/paper/' . $filename, $imgdata);

                array_push($names, '/images/paper/' . $filename);
            }
            $new_images = $names;
        }
        $images = array_merge($paper->images, $new_images);
        $data = array(
            'title' => $request['title'],
            'doc_date' => $request['doc_date'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'images' => $images,
            'company_id' => $request['company_id'],
            'user_id' => $user->id

        );

        $done =  $paper->update($data);
        if ($done)
            return response()->json([
                'response' =>  $paper
            ]);
        else
            return response()->json([
                'response' =>  2
            ], 422);
    }
}
