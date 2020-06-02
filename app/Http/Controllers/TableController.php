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
use App\Http\Traits\Fcm;
use App\User;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TableController extends Controller
{
    use Fcm;
    //
    private function getUser($token)
    {
        return Token::where('api_token', $token)->first()->user()->first();
    }

    public function addState(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

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
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'value' => 'required',
            'table' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

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
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $new_id = Role::max('id') + 1;
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
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

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
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];

        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $new_id = Company::max('id') + 1;

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
                'response' => 5
            ], 400);
        $id = $request['state_id'];
        $done =  State::where('id', $id)->first()->delete();
        if ($done)

            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'error' => 2
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
                'response' => 5
            ], 400);
        $id = $request['type_id'];
        $done =  Type::where('id', $id)->first()->delete();

        if ($done)

            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'error' => 2
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
                'response' => 5
            ], 400);
        $id = $request['role_id'];
        $done =  Role::where('id', $id)->first()->delete();

        if ($done)

            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'error' => 2
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
                'response' => 5
            ], 400);
        $id = $request['action_id'];
        $done =  Action::where('id', $id)->first()->delete();

        if ($done)

            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'error' => 2
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
                'response' => 5
            ], 400);
        $id = $request['company_id'];
        $done =  Company::where('id', $id)->first()->delete();

        if ($done)

            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'error' => 2
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
        if ($user->role_id == 3) {
            $notifies = Notify::with(['contract.type', 'book.type', 'user'])->where('notify_type', true)
                ->where('user_id', $user->id)
                ->where('type', true)
                ->where('seen', false)
                ->orderBy('updated_at', 'desc');
        } else if ($user->role_id == 2) {

            $notifies = Notify::with(['contract.type', 'book.type', 'user'])->where('role_id', $user->role_id)
                ->where('notify_type', false)->where('seen', false)
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->Where('notify_type', true)
                        ->where('seen', false);
                })->orderBy('updated_at', 'desc');
        } else {
            $notifies = Notify::with(['contract.type', 'book.type', 'user'])->where('role_id', $user->role_id)
                ->where('seen', false)
                ->where('notify_type', false)
                ->orderBy('updated_at', 'desc');
        }
        $notifies = $notifies->paginate(5);
        return response()->json([
            'response' => $notifies
        ]);
    }

    public function notifySeen(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $validator = Validator::make($request, [
            'id' => 'required',
        ]);
        if ($validator->fails())
            return response()->json([
                'response' => 5
            ], 400);

        $done = Notify::where('id', $request['id'])->first()->update(['seen' => true]);
        if ($done)
            return response()->json([
                'response' => 'done'
            ]);
        else
            return response()->json([
                'response' =>  2
            ], 422);
    }
    public function logout(Request $request)
    {
        if (!$request->bearerToken())
            return response()->json([
                'response' => 3
            ], 401);


        Token::where('api_token', $request->bearerToken())->first() ?
            Token::where('api_token', $request->bearerToken())->first()->delete() : null;
        return response()->json([
            'response' => 'done'
        ]);
    }
    public function showDocs(Request $request)
    {
        $request = json_decode($request->getContent(), true) ? json_decode($request->getContent(), true) : [];
        $user = $this->getUser($request->bearerToken());
      

        if (isset($request['doc_type'])) {
            switch ($request['doc_type']) {
                case "book":
                    $books = Book::where('deleted', false);
                    break;
                case "contract":
                    $contracts = Contract::where('deleted', false);
                    break;
                case "note":
                    $notes = Note::where('deleted', false);
                    break;
                case "paper":
                    $papers = Paper::where('deleted', false);
                    break;
            }
        } else {
            $books = Book::where('deleted', false);
            $contracts = Contract::where('deleted', false);
            $notes = Note::where('deleted', false);
            $papers = Paper::where('deleted', false);
        }

        if (isset($request['user_id'])) {
            if ($user->role_id != 1)
            $request['user_id'] = $user->id;
            

            if (isset($request['doc_type'])) {
                switch ($request['doc_type']) {
                    case "book":
                        $books =  $books->where('user_id', $request['user_id']);
                        break;
                    case "contract":
                        $contracts =  $contracts->where('user_id', $request['user_id']);
                        break;
                    case "note":
                        $notes =  $notes->where('user_id', $request['user_id']);
                        break;
                    case "paper":
                        $papers = $papers->where('user_id', $request['user_id']);
                        break;
                }
            } else {
                $books =  $books->where('user_id', $request['user_id']);
                $contracts =  $contracts->where('user_id', $request['user_id']);
                $notes =  $notes->where('user_id', $request['user_id']);
                $papers = $papers->where('user_id', $request['user_id']);
            }
        }
        if (isset($request['date_from']) && isset($request['date_to'])) {
            $date = new DateTime($request['date_from']);
            $date->modify('-1 day');
            $from = $date->format('Y-m-d');
            $to = $request['date_to'];

            if (isset($request['doc_type'])) {
                switch ($request['doc_type']) {
                    case "book":
                        $books = $books->whereBetween('doc_date', [$from . '%', $to . '%']);
                        break;
                    case "contract":
                        $contracts = $contracts->whereBetween('doc_date', [$from . '%', $to . '%']);
                        break;
                    case "note":
                        $notes = $notes->whereBetween('doc_date', [$from . '%', $to . '%']);
                        break;
                    case "paper":
                        $papers = $papers->whereBetween('doc_date', [$from . '%', $to . '%']);
                        break;
                }
            } else {
                $contracts = $contracts->whereBetween('doc_date', [$from . '%', $to . '%']);
                $books = $books->whereBetween('doc_date', [$from . '%', $to . '%']);
                $papers = $papers->whereBetween('doc_date', [$from . '%', $to . '%']);
                $notes = $notes->whereBetween('doc_date', [$from . '%', $to . '%']);
            }
        }

        if (isset($request['doc_type'])) {
            switch ($request['doc_type']) {
                case "book":
                    $books =  $books->get();
                    $merged = $books;
                    break;
                case "contract":
                    $contracts = $contracts->get();
                    $merged = $contracts;
                    break;
                case "note":
                    $notes =  $notes->get();
                    $merged = $notes;
                    break;
                case "paper":
                    $papers = $papers->get();
                    $merged = $papers;
                    break;
            }
        } else {
            $books =  $books->get();
            $contracts = $contracts->get();
            $notes =  $notes->get();
            $papers = $papers->get();
            $merged = $books->merge($contracts)->merge($notes)->merge($papers);
        }



        $merged = $merged->sortByDesc('created_at')->values()->all();
        if (isset($request['company_id']))
            $merged = collect($merged)->where('company_id', $request['company_id']);
        $merged = $this->myPaginate($merged, 15);
        return response()->json([
            'response' => $merged
        ]);
    }
    public function test(Request $request)
    {

        $book = Book::where('deleted', false)->get();
        $book = collect($book)->where('id', 11);
        return $book;
    }
    public function myPaginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
