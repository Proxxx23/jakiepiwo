<?php
declare( strict_types=1 );

use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return 'Strona w budowie...';
});

Route::get( '/api/questions', 'MainController@questionsData' );
Route::get( '/api/visitorName', 'MainController@visitorData' );
Route::post( '/api/results', 'AlgorithmController@presentStyles' );

Route::get(
    '/api/changelog', function () {
//    return view( 'changelog' );
}
);
