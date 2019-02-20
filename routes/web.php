<?php

Route::get( '/', 'MainController@index' );
Route::get( '/questions', 'MainController@index' );

Route::post( '/results', 'AlgorithmController@presentStyles' );

Route::get(
    '/changelog', function () {
    return view( 'changelog' );
}
);
