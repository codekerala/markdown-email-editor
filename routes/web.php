<?php

Route::get('api/email/create', 'EmailController@create');
Route::post('api/email/create', 'EmailController@send');

Route::get('/', function () {
    return view('welcome');
});
