<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\State;
use App\Role;
use App\Type;
use App\Action;
use App\Company;
use File;
use DateTime;
use Storage;
use App\Token;
use ImageOptimizer;
use App\Book;
use App\Contract;
use App\Note;
use App\Notify;
use App\Paper;

class TableController extends Controller
{
    //
    private function getUser($token)
    {
        return Token::where('api_token', $token)->first()->user()->first();
    }

    public function addState(Request $request)
    {
        $request =json_decode($request->getContent(), true);
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);

        $new_id = State::max('id') + 1;
        State::create([
            'id' => $new_id,
            'value' => $request['value']
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }
    public function addType(Request $request)
    {
        $request =json_decode($request->getContent(), true);
        $validator = Validator::make($request, [
            'value' => 'required',
            'table' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);

        $new_id = Type::max('id') + 1;
        Type::create([
            'id' => $new_id,
            'value' => $request['value'],
            'table' => $request['table'],
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }
    public function addRole(Request $request)
    {
        $request =json_decode($request->getContent(), true);
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        
        $new_id = Role::max('id');
        Role::create([
            'id' => $new_id,
            'value' => $request['value']
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }
    public function addAction(Request $request)
    {
        $request =json_decode($request->getContent(), true);
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);

        $new_id = Action::max('id') + 1;
        Action::create([
            'id' => $new_id,
            'value' => $request['value']
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }

    public function addCompany(Request $request)
    {
        $request =json_decode($request->getContent(), true);
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);

        $new_id = Company::max('id');

        Company::create([
            'id' => $new_id,
            'value' => $request['value']
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }



    // Add in tables ends

    //delete functions

    public function deleteState(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'state_id' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $id = $request['state_id'];
        State::where('id', $id)->first()->delete();

        return response()->json([
            'response' => 'done'
        ]);
    }

    public function deleteType(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'type_id' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $id = $request['type_id'];
        Type::where('id', $id)->first()->delete();

        return response()->json([
            'response' => 'done'
        ]);
    }

    public function deleteRole(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'role_id' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $id = $request['role_id'];
        Role::where('id', $id)->first()->delete();

        return response()->json([
            'response' => 'done'
        ]);
    }

    public function deleteAction(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'action_id' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $id = $request['action_id'];
        Action::where('id', $id)->first()->delete();

        return response()->json([
            'response' => 'done'
        ]);
    }
    public function deleteCompany(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'company_id' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'errors' => $validator->errors()
            ]);
        $id = $request['company_id'];
        Company::where('id', $id)->first()->delete();

        return response()->json([
            'response' => 'done'
        ]);
    }

    public function showStates()
    {
        $states = State::all();
        return response()->json([
            'response' => $states
        ]);
    }

    public function showTypes()
    {
        $types = Type::all();
        return response()->json([
            'response' => $types
        ]);
    }

    public function showActions()
    {
        $actions = Action::all();
        return response()->json([
            'response' => $actions,
        ]);
    }

    public function showRoles()
    {
        $roles = Role::all();
        return response()->json([
            'response' => $roles,
        ]);
    }

    public function showCompanies()
    {
        $companies = Company::all();
        return response()->json([
            'response' => $companies,
        ]);
    }

  

    public function showCounts()
    {
        $allBooks = Book::count();
        $allContracts = Contract::count();
        $allPapers = Paper::count();
        $allNotes = Note::count();

        $acceptBooks = Book::where('state_id', 1)->count();
        $acceptContracts = Contract::where('state_id', 1)->count();
      

        $rejectBooks = Book::where('state_id', 2)->count();
        $rejectContracts = Contract::where('state_id', 2)->count();

        $waitBooks = Book::where('state_id', 3)->count();
        $waitContracts = Contract::where('state_id', 3)->count();
        
      
        return response()->json([
            'response' => [
                'books' => [
                    'total' => $allBooks,
                    'accepted' => $acceptBooks,
                    'rejected' => $rejectBooks,
                    'waiting' => $waitBooks
                ],
                'contracts' => [
                    'total' => $allContracts,
                    'accepted' => $acceptContracts,
                    'rejected' => $rejectContracts,
                    'waiting' => $waitContracts
                ],
                'papers' => [
                    'total' => $allPapers
                ],
                'notes' => [
                    'total' => $allNotes
                ]
            ]
        ]);
    }

    public function showNotify(Request $request)
    {
        $user = $this->getUser($request->bearerToken());
        $notifies = null;
        if ($user->role_id == 3){
            $notifies = Notify::with(['contract', 'book', 'user'])->where('seen', true)
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc');
        }else if ($user->role_id == 2) {
            
            $notifies = Notify::with(['contract', 'book', 'user'])->where('role_id', $user->role_id)
            ->where('seen', false)
            ->orWhere(function ($query) use($user) {
                $query->where('user_id', $user->id)
                      ->Where('seen', true);
            })->orderBy('updated_at', 'desc');
        }else {
            $notifies = Notify::with(['contract', 'book', 'user'])->where('role_id', $user->role_id)
            ->where('seen' , false)->orderBy('updated_at', 'desc');
        }
     $notifies = $notifies->paginate(5);
     return response()->json([
        'response' => $notifies
     ]);
    }

    public function test(Request $request)
    {
        $img =array();
        return $img;
        $pathToImage = public_path(). "/images/paper/5.jpg";
        $te =public_path(). "/images/note/1.jpeg";
      
          ImageOptimizer::optimize($pathToImage, $te);
        return "true";
      
    }
}
