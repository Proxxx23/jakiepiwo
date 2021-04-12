<?php
declare( strict_types=1 );

use App\Http\Controllers\OnTapController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\UntappdCronController;
use Illuminate\Support\Facades\Route;

Route::get( '/', static fn(): string => 'API version 2.0' );

Route::get( '/questions', [ QuestionsController::class, 'handle' ] );
Route::post( '/results', [ ResultsController::class, 'resultsAction' ] );
Route::get( '/results/{resultsHash}', [ ResultsController::class, 'resultsByResultsHashAction' ] );
Route::post( '/ontap', [ OnTapController::class, 'handle' ] );
Route::get( '/untappd/fetch', [ UntappdCronController::class, 'handle' ] );
