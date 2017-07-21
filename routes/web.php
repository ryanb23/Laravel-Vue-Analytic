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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::resource('rule','RuleController');

Route::post('/rule/fileupload', 'RuleController@FileUpload');
Route::get('/rule/activate/{id}', ['uses' => 'RuleController@activate', 'as' => 'rule.activate']);
Route::get('/rule/deactivate/{id}', ['uses' => 'RuleController@deactivate', 'as' => 'rule.deactivate']);
