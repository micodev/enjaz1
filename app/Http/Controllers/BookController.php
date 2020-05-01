<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Book;
use App\Token;
use Validator;
use File;
use DateTime;
use Illuminate\Support\Str;

class bookController extends Controller
{
    private function getUser($token)
    {
        return Token::where('api_token', $token)->first()->user()->first();
    }
    public function create(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
       
        //  $user = User::where('id', '1')->first();
        $request = json_decode($request->getContent(), true);

        $validator = Validator::make($request, [
            'type_id' => 'required| integer',
            'doc_date' => 'required',
            'note' => 'required',
            'company_id' => 'required| integer',
            'doc_number' => 'required',
           // 'doc_number' => 'required | unique:books',
            'destination' => 'required',
            'action_id' => 'required| integer',
            'title' => 'required',

        ]);

        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);


        $images = [];
        if (isset($request['images'])) {
            if (!file_exists(public_path() . '/images/book')) {
                File::makeDirectory(public_path() . '/images/book');
            }
            $names = [];
            foreach ($request['images'] as $image) {

                $image = explode(',', $image)[1];

                $imgdata = base64_decode($image);

                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $type = explode('/', $mime_type)[1];
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/book/' . $filename, $imgdata);

                array_push($names, '/images/book/' . $filename);
            }
            $images = $names;
        }

        $b =  Book::create([
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => 3, //if u need default
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title']

        ]);

        return response()->json([
            'response' => 'done'
        ]);
    }

    public function showBooks()
    {
        //  $user = $this->getUser($request->bearerToken());
        $books = Book::with(['company', 'type', 'state', 'user', 'action'])->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $books
        ]);
    }

    public function delete(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        $id = $request['id'];
        $book = Book::where('id', $id)->first()->delete();
        return response()->json([
            'response' => 'done'
        ]);
    }



    public function deleteImage(Request $request)
    {
        $request = json_decode($request->getContent(), true);

        $id = $request['book_id'];
        $path = $request['img_path'];
        $book = Book::where('id', $id)->first();
        $images = $book->images;

        $images =   array_diff($images, [$path]);
        $book->images = $images;
        $book->save();
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

        $books = Book::with(['company', 'type', 'state', 'user', 'action'])->orderBy('created_at', 'desc');
        if (isset($request['company_id'])) {

            $books = $books->where('company_id', $request['company_id']);
            $empty = false;
        }

        if (isset($request['title'])) {
            $books = $books->where('title', 'like', '%' . $request['title'] . '%');
            $empty = false;
        }

        if (isset($request['destination'])) {
            $books = $books->where('destination', 'like', '%' . $request['destination'] . '%');
            $empty = false;
        }


        if (isset($request['type_id'])) {
            $books = $books->where('type_id', $request['type_id']);
            $empty = false;
        }

        if (isset($request['state_id'])) {
            $books = $books->where('state_id', $request['state_id']);
            $empty = false;
        }

        if (isset($request['action_id'])) {
            $books = $books->where('action_id', $request['action_id']);
            $empty = false;
        }
        if (isset($request['doc_number'])) {
            $books = $books->where('doc_number', $request['doc_number']);
            $empty = false;
        }

        if (isset($request['date_from']) && isset($request['date_to'])) {
            $empty = false;
            $date = new DateTime($request['date_from']);
            $date->modify('-1 day');
            $from = $date->format('Y-m-d');
            $to = $request['date_to'];

            $books = $books->whereBetween('doc_date', [$from . '%', $to . '%']);
        }

        if ($empty)
            return response()->json([
                'response' => 'Bad Request'
            ]);
        $books = $books->paginate(5);
        return response()->json([
            'response' => $books
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        // $user = User::where('id', '1')->first();
        $request = json_decode($request->getContent(), true);

        $validator = Validator::make($request, [
            'type_id' => 'required | integer',
            'doc_date' => 'required',
            'note' => 'required',
            'company_id' => 'required | integer',
            'doc_number' => 'required',
            'destination' => 'required',
            'action_id' => 'required | integer',
            'title' => 'required',
            'state_id' => 'required | integer'

        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $book = Book::where('id', $request['id'])->first();
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
                File::put(public_path() . '/images/book/' . $filename, $imgdata);

                array_push($names, '/images/book/' . $filename);
            }
            $new_images = $names;
        }
        $images = array_merge($book->images, $new_images);
        $data = array(
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title'],
            'state_id' => $request['state_id']


        );

        $book->update($data);
        return response()->json([
            'response' =>  $book
        ]);
    }

    public function waitBooks()
    {
        $books = Book::with(['company', 'type', 'state', 'user', 'action'])->where('state_id', '3')
            ->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $books
        ]);
    }
    public function changeState(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        $book = Book::where('id', $request['id'])->first();
        $book->state_id = $request['state_id'];
        $book->save();
        return response()->json([
            'response' => 'done'
        ]);
    }
}
