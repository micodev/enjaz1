<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\State;
use App\Role;
use App\Type;
use App\Action;
use App\Company;

class TableController extends Controller
{
    //

    public function addState(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
        return response()->json([
            'errors' => $validator->errors()
        ]);


        State::create([
            'value' => $request['value']
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }
    public function addType(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'value' => 'required',
            'table' => 'required'
        ]);
        if ($validator->fails())
        return response()->json([
            'errors' => $validator->errors()
        ]);


        Type::create([
            'value' => $request['value'],
            'table' => $request['table'],
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }
    public function addRole(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
        return response()->json([
            'errors' => $validator->errors()
        ]);


        Role::create([
            'value' => $request['value']
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }
    public function addAction(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
        return response()->json([
            'errors' => $validator->errors()
        ]);


        Action::create([
            'value' => $request['value']
        ]);

        return response()->json([
            'resopnse' => 'done'
        ]);
    }

    public function addCompany(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'value' => 'required'
        ]);
        if ($validator->fails())
        return response()->json([
            'errors' => $validator->errors()
        ]);


        Company::create([
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


}
