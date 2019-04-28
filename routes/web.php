<?php
declare( strict_types=1 );

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Strona w budowie...';
});

Route::get( '/questions', 'MainController@questionsData' );
Route::get( '/visitorName', 'MainController@visitorData' );
Route::post( '/results', 'AlgorithmController@presentStyles' );

Route::get(
    '/changelog', function () {
    return view( 'changelog' );
}
);
