<?php

Route::get('/', 'StylePickerController@showQuestions')->middleware('betatesty');
Route::post('/results', 'StylePickerController@mix');
Route::get('/changelog', function() {
	return view('changelog');
});
