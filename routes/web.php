<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Notifications\NewBlogCreated;

Route::get('/', 'PagesController@welcome');
Route::get('/pink', 'PagesController@pink');
Route::get('/blue', 'PagesController@blue');

// advanced auth
Route::get('/auth/LEO', 'AuthController@login');
Route::post('/auth/LEO', 'AuthController@postLogin');
Route::get('/auth/token/{token}', 'AuthController@authenticate');

Route::get('/notify', function() {
    auth()->user()->notify(new NewBlogCreated);
    return view('notify');
});
// couldn't get a real world example working but it does go to log to the database

// assumes you're signed in and then proceeds with the request
Route::middleware('auth')->post('/colabs', function() {
    auth()->user()->colab()->create(
        request()->validate(['name' => 'required'])
    );
    // user_id is now set automatically
    return redirect('/');
});
/*
 * GET /blogs (index)
 * GET /blogs/create (create)
 * GET /blogs/1 (show)
 * POST /blogs (store)
 * GET /blogs/1/edit (edit)
 * PATCH /blogs/1 (update)
 * DELETE /blogs/1 (destroy)
 */

// Tracking Document
//Auth::loginUsingId(1);
Route::group(['middleware' => ['web']], function () {
Route::get('/documents/{document}', 'DocumentsController@show');
});

Route::resource('/blogs', 'BlogsController');

Route::post('/blogs/{blog}/comments', 'BlogsCommentsController@store');
Route::patch('/comments/{comment}', 'BlogsCommentsController@update');

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
