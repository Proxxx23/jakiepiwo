<?php
declare( strict_types=1 );

use Illuminate\Support\Facades\Route;

header('Access-Control-Allow-Origin: *');

Route::get('/', static function () {
    return 'API version 2.0';
    // doc
});

Route::get( '/questions', 'QuestionsController@handle' );
Route::post( '/results', 'ResultsController@resultsAction' );
Route::get( '/results/{resultsHash}', 'ResultsController@resultsByResultsHashAction' );
Route::post( '/ontap', 'OntapController@handle' );
