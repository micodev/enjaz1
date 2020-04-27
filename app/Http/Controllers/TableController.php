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

class TableController extends Controller
{
    //

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

    public function test(Request $request)
    {
        if (!file_exists(public_path() . '/images/book')) {
            File::makeDirectory(public_path(). '/images/book');
        }

        // File::makeDirectory(public_path(). "/tesst");
        return "true";
//         return Company::max('id') + 1;

//         $date = new DateTime('2016-04-01');
// $date->modify('-1 day');
// return $date->format('Y-m-d');

      
    }
}
