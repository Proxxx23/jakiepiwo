<?php declare( strict_types=1 )

Route::get( '/', 'MainController@index' );
Route::get( '/questions', 'MainController@index' );

Route::get( '/results', 'AlgorithmController@presentStyles' );

Route::get(
    '/changelog', function () {
    return view( 'changelog' );
}
);
