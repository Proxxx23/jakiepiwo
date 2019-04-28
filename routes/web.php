<?php
declare( strict_types=1 );

use Illuminate\Support\Facades\Route;

Route::get( '/questions', 'MainController@indexData' );

Route::post( '/results', 'AlgorithmController@presentStyles' );

Route::get(
    '/changelog', function () {
    return view( 'changelog' );
}
);
