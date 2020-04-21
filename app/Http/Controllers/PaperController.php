<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Token;
use App\Paper;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

use Request as Req;

class paperController extends Controller
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
       // $request = $request->json()->all();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'doc_date' => 'required',
            'note' => 'required',
            'company_id' => 'required'
        ]);
        //  return $request;
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        //  dd($request->image);
        $images = '';
        if ($request->hasFile('images')) {
            $names = [];
            foreach ($request->images as $image) {


                $Path = public_path() . '/images/paper/';
                $filename = time() . $image->getClientOriginalName();
                $ex = $image->getClientOriginalExtension();
                $image->move($Path, $filename);

                array_push($names, '/images/paper/' . $filename);
            }
            $images = $names;
        }


        $p =  Paper::create([
            'title' => $request['title'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'company_id' => $request['company_id'],
            'user_id' => $user->id,
            'images' => $images

        ]);
      //  return $p->images;
        return response()->json([
            'response' => 'done'
        ]);
    }

    public function showPapers()
    {
        $papers = Paper::orderBy('created_at', 'desc')->paginate(5);

        return response()->json([
            'response' => $papers
        ]);
    }

    public function delete(Request $request)
    {
        $request = $request->json()->all();

        $id = $request['id'];
        $paper = Paper::where('id', $id)->first()->delete();
        return response()->json([
            'response' => 'done'
        ]);
    }

    public function search(Request $request)
    {
        $request = $request->json()->all();


        // $title = $request['title'];
      
        // $company_id = $request['company_id'];
        $papers = Paper::with(['company', 'user'])->orderBy('created_at', 'desc');
        $empty = true;
        if (isset($request['company_id'])){
            $papers = $papers->where('company_id', $request['company_id']);
            $empty = false;
            
        }
        if (isset($request['title'])){
            $papers = $papers->where('title', 'like', '%' . $request['title'] . '%');
            $empty = false;

        }
        if (isset($request['date_from']) && isset($request['date_to'])) {

            $from = $request['date_from'];
            $to = $request['date_to'];

            $papers = $papers->whereBetween('doc_date', [$from . '%', $to . '%']);
            $empty = false;
        }
        if ($empty)
        return response()->json([
            'response' => 'Bad Request'
        ]);

        $papers = $papers->paginate(5);

        return response()->json([
            'response' => $papers
        ]);
    }

    public function searchPaper(Request $request)
    {
        $request = $request->json()->all();

        $papers = Paper::with(['company', 'user'])->orderBy('created_at', 'desc');
        if (isset($request['title']))
            $papers = $papers->where('title', 'like', '%' . $request['title'] . '%');
        if (isset($request['date_from']) && isset($request['date_to'])) {
            $from = $request['date_from'];
            $to = $request['date_to'];
            $papers = $papers->whereBetween('doc_date', [$from . '%', $to . '%']);
        }

        $papers = $papers->paginate(1);
        return response()->json([
            'response' => $papers
        ]);
    }

    public function deleteImage(Request $request)
    {
       

        $id = $request['paper_id'];
        $paper = Paper::where('id', $id)->first();
        $images = $paper->images;

        $path = $request['img_path'];
        $images =   array_diff($images, [$path]);
        $paper->images = $images;
        $paper->save();
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    public function update(Request $request)
    {
        //  $user = $this->getUser($request->bearerToken());
        
        $user = User::where('id', '1')->first();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'title' => 'required',
            'doc_date' => 'required',
            'note' => 'required',
            'company_id' => 'required'
        ]);
        //  return $request;
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        //  dd($request->image);
        $images = [];
        if ($request->hasFile('images')) {
            $names = [];
            foreach ($request->images as $image) {


                $Path = public_path() . '/images/paper/';
                $filename = time() . $image->getClientOriginalName();
                $ex = $image->getClientOriginalExtension();
                $image->move($Path, $filename);

                array_push($names, '/images/paper/' . $filename);
            }
            $images = $names;
        }

        $data = array(
            'title' => $request['title'],
            'doc_date' => $request['doc_date'],
            'note' => $request['note'],
            'images' => $images,
            'company_id' => $request['company_id'],
            'user_id' => $user->id

        );

        Paper::where('id', $request['id'])->update($data);
        return response()->json([
            'response' => 'done'
        ]);
    }
}


  // $from    = Carbon::parse('2020-02-21')
        //     ->startOfDay()        // 2018-09-29 00:00:00.000000
        //     ->toDateTimeString(); // 2018-09-29 00:00:00
        // //  BookingDates::where('email', Input::get('email'))
        // //  ->orWhere('name', 'like', '%' . Input::get('name') . '%')->get();
        // return $from;
        // //         $from    = Carbon::parse($request->from)
        // //                  ->startOfDay()        // 2018-09-29 00:00:00.000000
        // //                  ->toDateTimeString(); // 2018-09-29 00:00:00

        // // $to      = Carbon::parse($request->to)
        // //                  ->endOfDay()          // 2018-09-29 23:59:59.000000
        // //                  ->toDateTimeString(); // 2018-09-29 23:59:59

        // // $models  = Model::whereBetween('created_at', [$from, $to])->get();

        // //     }

        // //use Illuminate\Support\Carbon;
