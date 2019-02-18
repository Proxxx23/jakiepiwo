<?php

Route::get('/', 'StylePickerController@showQuestions');
Route::get('/questions', 'StylePickerController@showQuestions');
Route::post('/results', 'StylePickerController@mix');
Route::get('/changelog', function() {
	return view('changelog');
});
