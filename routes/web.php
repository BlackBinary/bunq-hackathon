<?php

Route::get('/', 'WebController@index')->name('home');
Route::get('/createreceipt', 'WebController@createReceipt')->name('newReceipt');
Route::get('/getreceipt/{hash}', 'WebController@getReceipt')->name('getReceipt');
