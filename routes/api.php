<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', 'TableController@test');

Route::middleware(['cors'])->group(function () {
    Route::post('/login/user', 'UserController@login');
});

Route::middleware(['cors', 'isActive', 'isNotUser'])->group(function () {

    Route::post('/delete/paper', 'PaperController@delete');
    Route::post('/delete/book', 'BookController@delete');
    Route::post('/delete/note', 'NoteController@delete');
    Route::post('/delete/contract', 'ContractController@delete');

    Route::post('/delete/state', 'TableController@deleteState');
    Route::post('/delete/type', 'TableController@deleteType');
    Route::post('/delete/role', 'TableController@deleteRole');
    Route::post('/delete/action', 'TableController@deleteAction');
    Route::post('/delete/company', 'TableController@deleteCompany');

    // accepts
    Route::get('/state/books', 'BookController@waitBooks');
    Route::get('/state/contracts', 'ContractController@waitContracts');

    Route::post('/state/book', 'BookController@changeState');
    Route::post('/state/contract', 'ContractController@changeState');
});
Route::middleware(['cors', 'isActive'])->group(function () {


    Route::post('/search', 'PaperController@search');
    Route::put('/update', 'PaperController@update');

    // below 6 routes about user

    Route::post('/register/user', 'UserController@register');
    Route::post('/delete/user', 'UserController@delete');
    Route::put('/update/user', 'UserController@update');
    Route::get('/show/users', 'UserController@show');
    Route::post('/search/users', 'UserController@search');





    Route::post('/add/paper', 'PaperController@create');
    Route::post('/add/book', 'BookController@create');
    Route::post('/add/note', 'NoteController@create');
    Route::post('/add/contract', 'ContractController@create');

    Route::post('/create/book', 'BookController@createBook');
    Route::post('/create/contract', 'ContractController@createContract');



    Route::get('/show/papers', 'PaperController@showPapers');
    Route::get('/show/books', 'BookController@showBooks');
    Route::get('/show/notes', 'NoteController@showNotes');
    Route::get('/show/contracts', 'ContractController@showContracts');

    Route::post('/delete/image/paper', 'PaperController@deleteImage');
    Route::post('/delete/image/book', 'BookController@deleteImage');
    Route::post('/delete/image/note', 'NoteController@deleteImage');
    Route::post('/delete/image/contract', 'ContractController@deleteImage');

    Route::post('/search/papers', 'PaperController@search');
    Route::post('/search/books', 'BookController@search');
    Route::post('/search/notes', 'NoteController@search');
    Route::post('/search/contracts', 'ContractController@search');


    Route::put('/update/paper', 'PaperController@update');
    Route::put('/update/book', 'BookController@update');
    Route::put('/update/note', 'NoteController@update');
    Route::put('/update/contract', 'ContractController@update');

    /////below routes for other tables

    //add in tables

    Route::post('/add/state', 'TableController@addState');
    Route::post('/add/type', 'TableController@addType');
    Route::post('/add/role', 'TableController@addRole');
    Route::post('/add/action', 'TableController@addAction');
    Route::post('/add/company', 'TableController@addCompany');

    //delete 


    //shows

    Route::get('/show/states', 'TableController@showStates');
    Route::get('/show/types', 'TableController@showTypes');
    Route::get('/show/roles', 'TableController@showRoles');
    Route::get('/show/actions', 'TableController@showActions');
    Route::get('/show/companies', 'TableController@showCompanies');




    // below part one of project

    Route::post('/edit/book', 'BookController@edit');
    Route::post('/edit/contract', 'ContractController@edit');

    Route::get('/show/counts', 'TableController@showCounts');
    Route::get('/show/notify', 'TableController@showNotify');
    Route::post('/seen/notify', 'TableController@notifySeen');

    Route::get('/logout/user', 'TableController@logout');
});
