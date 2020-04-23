<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Book;
use App\Token;
use Validator;

class bookController extends Controller
{
    private function getUser($token)
    {
        $id = Token::where('api_token', $token)->first()->user_id;
        return User::where('id', $id)->first();
    }
    public function create(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        //  $user = User::where('id', '1')->first();
        $request = $request->json()->all();

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


        $images = '';
        if (isset($request['images'])) {
            $names = [];
            foreach ($request['images'] as $image) {


                // $Path = public_path() . '/images/book/';
                // $filename = time() . $image->getClientOriginalName();
                // $ex = $image->getClientOriginalExtension();
                // $image->move($Path, $filename);

                // array_push($names, '/images/book/' . $filename);

                $image = explode(',', $image)[1];
       
                $imgdata = base64_decode($image);
               
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                //return $mime_type;
                $type = explode('/', $mime_type)[1];
        
                //  $image = str_replace(' ', '+', $image);
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/book/' . $filename, $imgdata);

                array_push($names, '/images/book/' . $filename);
            }
            $images = $names;
        }
        // return $images;
        $b =  Book::create([
            'type_id' => $request['type_id'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images,
            'state_id' => '4', //if u need default
            'destination' => $request['destination'],
            'doc_number' => $request['doc_number'],
            'action_id' => $request['action_id'],
            'title' => $request['title']

        ]);
        //  return json_decode($b->images);
        return response()->json([
            'response' => 'done'
        ]);
    }

    public function showBooks()
    {
        //  $user = $this->getUser($request->bearerToken());
        $books = Book::orderBy('created_at', 'desc')->paginate(1);
        return response()->json([
            'response' => $books
        ]);
    }

    public function delete(Request $request)
    {
        $request = $request->json()->all();
        $id = $request['id'];
        $book = Book::where('id', $id)->first()->delete();
        return response()->json([
            'response' => 'done'
        ]);
    }



    public function deleteImage(Request $request)
    {
        $request = $request->json()->all();

        $id = $request['book_id'];
        $path = $request['img_path'];
        $book = Book::where('id', $id)->first();
        $images = $book->images;

        // $path = "/images/book/15870461461adad54ddb6b9b9008426595297b0329.jpg";
        $images =   array_diff($images, [$path]);
        $book->images = $images;
        $book->save();
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    public function searchBook(Request $request) // for mobile
    {
        $books = Book::with(['company', 'type', 'state', 'user', 'action'])->orderBy('created_at', 'desc');
        if (isset($request['title']))
            $books = $books->where('title', 'like', '%' . $request['title'] . '%');
        if (isset($request['destination']))
            $books = $books->where('destination', 'like', '%' . $request['destination'] . '%');
        if (isset($request['action_id']))
            $books = $books->where('action_id', $request['action_id']);
        if (isset($request['type_id']))
            $books = $books->where('type_id', $request['type_id']);
        if (isset($request['company_id']))
            $books = $books->where('company_id', $request['company_id']);
        if (isset($request['date_from']) && isset($request['date_to'])) {

            $from = $request['date_from'];
            $to = $request['date_to'];

            $books = $books->whereBetween('doc_date', [$from . '%', $to . '%']);
        }
        $books = $books->paginate(1);
        return response()->json([
            'response' => $books
        ]);
    }

    public function search(Request $request)
    {
        $request = $request->json()->all();

        // $title = $request['title'];
        // $destination = $request['destination'];
        // $doc_number = $request['doc_number'];
        // $type = $request['type_id'];
        // $state = $request['state_id'];
        // $company_id = $request['company_id'];
        // $action = $request['action_id'];
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
            $from = $request['date_from'];
            $to = $request['date_to'];

            $books = $books->whereBetween('doc_date', [$from . '%', $to . '%']);
        }

        if ($empty)
            return response()->json([
                'response' => 'Bad Request'
            ]);
        $books = $books->paginate(1);
        return response()->json([
            'response' => $books
        ]);
    }

    public function update(Request $request)
    {
        //  $user = $this->getUser($request->bearerToken());
        $user = User::where('id', '1')->first();
        $request = $request->json()->all();

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


                // $Path = public_path() . '/images/book/';
                // $filename = time() . $image->getClientOriginalName();
                // $ex = $image->getClientOriginalExtension();
                // $image->move($Path, $filename);

                // array_push($names, '/images/book/' . $filename);

                $image = explode(',', $image)[1];
       
                $imgdata = base64_decode($image);
               
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                //return $mime_type;
                $type = explode('/', $mime_type)[1];
        
                //  $image = str_replace(' ', '+', $image);
                $filename = time() . Str::random(2) . '.' . $type;
                File::put(public_path() . '/images/book/' . $filename, $imgdata);

                array_push($names, '/images/book/' . $filename);
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

        Book::where('id', $request['id'])->update($data);
        return response()->json([
            'response' => 'done'
        ]);
    }
}
