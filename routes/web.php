<?php
declare( strict_types=1 );

use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return 'API version 2.0';
    // doc
});

Route::get( '/questions', 'QuestionsController@handle' );
Route::get( '/visitorName', 'VisitorController@handle' );
Route::post( '/results', 'AlgorithmController@handle' );
Route::post( '/ontap', 'OntapController@handle' );
