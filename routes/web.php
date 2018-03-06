<?php

Route::get('/', 'StylePickerController@showQuestions')->middleware('betatesty');
Route::post('/results', 'StylePickerController@mix')->middleware('betatesty');
