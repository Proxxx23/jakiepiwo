<?php

Route::get('/', 'StylePickerController@showQuestions');
Route::post('/results', 'StylePickerController@mix');
