<?php

Route::get('/', 'StylePickerController@showQuestions');
Route::post('/basic', 'StylePickerController@mix');
