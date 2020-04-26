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

    public function test(Request $request)
    {

        $date = new DateTime('2016-04-01');
$date->modify('-1 day');
return $date->format('Y-m-d');

        // return $request->getContent();
        // $image = "data:image/png;base64,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"; // your base64 encoded
        // //$image = str_replace('data:image/png;base64,', '', $image);
        // $image = explode(',', $image)[1];
       
        // $imgdata = base64_decode($image);
       
        // $f = finfo_open();
        // $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
        // //return $mime_type;
        // $type = explode('/', $mime_type)[1];

        // //  $image = str_replace(' ', '+', $image);
        // $imageName = time() . '.' . $type;
        // File::put(public_path() . '/images/paper/' . $imageName, $imgdata);

        // return "true";


        // return response()->json([
        //     'header' => $request->headers->all(),
        //     'body' => $request->all()

        // ]);
        // if ($request->all())
        //     return response()->json([
        //         "respone" => [
        //             "type" => "form data",
        //             "header" => $request->headers->all()
        //         ]
        //     ]);
        // else if ($request->json()->all() != null) {
        //     return response()->json([
        //         "respone" => [
        //             "type" => "json",
        //             "header" => $request->headers->all()
        //         ]
        //     ]);
        // }

        // return response()->json([
        //     "response" => "false"
        // ]);
    }
}
