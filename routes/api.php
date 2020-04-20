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


Route::post('/search', 'PaperController@search');
Route::put('/update', 'PaperController@update');

Route::post('/login/user', 'LoginController@userLogin');
Route::post('/register/user', 'RegisterController@userRegister');

Route::post('/add/paper', 'PaperController@create');
Route::post('/add/book', 'BookController@create');
Route::post('/add/note', 'NoteController@create');
Route::post('/add/contract', 'ContractController@create');

Route::post('/delete/paper', 'PaperController@delete');
Route::post('/delete/book', 'BookController@delete');
Route::post('/delete/note', 'NoteController@delete');
Route::post('/delete/contract', 'ContractController@delete');

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

//use below 4 routes with mobile if above not works
Route::post('/search/mobile/papers', 'PaperController@search');
Route::post('/search/mobile/books', 'BookController@search');
Route::post('/search/mobiel/notes', 'NoteController@search');
Route::post('/search/mobile/contracts', 'ContractController@search');
// end form mobile
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

Route::post('/delete/state', 'TableController@deleteState');
Route::post('/delete/type', 'TableController@deleteType');
Route::post('/delete/role', 'TableController@deleteRole');
Route::post('/delete/action', 'TableController@deleteAction');
Route::post('/delete/company', 'TableController@deleteCompany');

//shows

Route::get('/show/states', 'TableController@showStates');
Route::get('/show/types', 'TableController@showTypes');
Route::get('/show/roles', 'TableController@showRoles');
Route::get('/show/actions', 'TableController@showActions');
Route::get('/show/companies', 'TableController@showCompanies');