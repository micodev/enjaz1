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
use App\Notify;
use App\Http\Traits\Fcm;

class bookController extends Controller
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
            'type_id' => 'required| integer',
            'doc_date' => 'required',
            'company_id' => 'required| integer',
            'doc_number' => 'required',
            // 'doc_number' => 'required | unique:books',
            'destination' => 'required',
            'action_id' => 'required| integer',
            'title' => 'required',

        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);


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

        $book =  Book::create([
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => 3,
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title'],
            'body' => isset($request['body']) ? $request['body'] : ""

        ]);


        if ($user->role_id == 3) {
            Notify::create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'type' => false,
                'role_id' => 2,

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

    public function createBook(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];


        $validator = Validator::make($request, [
            'type_id' => 'required| integer',
            'doc_date' => 'required',
            'company_id' => 'required| integer',
            'doc_number' => 'required',
            // 'doc_number' => 'required | unique:books',
            'destination' => 'required',
            'action_id' => 'required| integer',
            'title' => 'required',

        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);




        $book =  Book::create([
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'state_id' => 3, //if u need default
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title'],
            'body' => isset($request['body']) ? $request['body'] : ""

        ]);


        if ($user->role_id != 1) {
            Notify::create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'type' => true,
                'role_id' => 1

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

    public function showBooks(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        // return all books if admin 
        // return secrt book if user or super
        if ($user->role_id == 1) {
            $books = Book::with(['company', 'type', 'state', 'user', 'action'])
                ->where('deleted', false)
                ->where('state_id', 1)
                ->orderBy('created_at', 'desc')->paginate(5);
            return response()->json([
                'response' => $books
            ]);
        } else {
            $books = Book::with(['company', 'type', 'state', 'user', 'action'])
                ->where('deleted', false)
                ->where('state_id', 1)
                ->where('type_id', '!=', 3)
                ->orWhere(function ($query) use ($user) {
                    $query->Where('type_id', 3)
                        ->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')->paginate(5);
            return response()->json([
                'response' => $books
            ]);
        }
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
        $book = Book::where('id', $id)->first();
        if (!$book)
            return response()->json([
                'response' => 2
            ], 422);

        $done = $book->update([
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
            'book_id' => 'required',
            'img_path' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $id = $request['book_id'];
        $path = $request['img_path'];
        $book = Book::where('id', $id)->first();
        if (!$book)
            return response()->json([
                'response' => 2
            ], 422);
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
        $user = $this->getUser($request->bearerToken());

        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $empty = true;

        $books = Book::with(['company', 'type', 'state', 'user', 'action'])
            ->where('deleted', false)
            ->where('state_id', 1);

        if ($user->role_id != 1) {
            $books = $books->where('type_id', '!=', 3)
                ->orWhere(function ($query) use ($user) {
                    $query->Where('type_id', 3)
                        ->where('user_id', $user->id);
                });
        }

        $books = $books->orderBy('created_at', 'desc');
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
                'response' => 4
            ], 400);
        $books = $books->paginate(5);
        return response()->json([
            'response' => $books
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        // $user = User::where('id', '1')->first();
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];


        $validator = Validator::make($request, [
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
            'note' => isset($request['note']) ? $request['note'] : "",
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title'],
            'state_id' => $request['state_id']


        );
        if ($user->role_id == 1)
            $data['body'] = isset($request['body']) ? $request['body'] : "";
        $done =  $book->update($data);
        if ($done)
            return response()->json([
                'response' =>  $book
            ]);
        else
            return response()->json([
                'response' => 2
            ], 422);
    }

    public function waitBooks()
    {
        $books = Book::with(['company', 'type', 'state', 'user', 'action'])->where('state_id', 3)
            ->orderBy('created_at', 'desc')->paginate(5);
        return response()->json([
            'response' => $books
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
        $done = Book::where('id', $request['id'])->first()->update([
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
            'book' => 'required',
            'user' => 'required'

        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $validator = Validator::make($request['book'], [
            'type_id' => 'required | integer',
            'doc_date' => 'required',
            'company_id' => 'required | integer',
            'doc_number' => 'required',
            'destination' => 'required',
            'action_id' => 'required | integer',
            'title' => 'required',
            'state_id' => 'required | integer',
        ]);

        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $images = [];
        if ($request['book']['images'] != null) {
            if (!file_exists(public_path() . '/images/book')) {
                File::makeDirectory(public_path() . '/images/book');
            }
            $names = [];
            foreach ($request['book']['images'] as $image) {

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

        $request['book']['images'] = $images;

        $notify =  Notify::where('id', $request['id'])->first()->update(['seen' => $request['seen']]);
        $user = User::with(['tokens' => function ($q) {
            $q->where('notify_token', '!=', null);
        }])->where('id', $request['user']['id'])->first();

        if (count($user->tokens) > 0) {
            foreach ($user->tokens as $tokens) {
                $this->NotifyState($tokens->notify_token, $request['book']['title'], $request['book']['state_id'] == 1 ? true : false);
            }
        }


        $book = Book::where('id', $request['book']['id'])->first();
        $done =  $book->update($request['book']);
        if ($done)
            return response()->json([
                'response' =>  $book
            ]);
        else
            return response()->json([
                'response' =>  2
            ], 422);
    }
}
