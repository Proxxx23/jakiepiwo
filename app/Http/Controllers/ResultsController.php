<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Services\SimpleResultsService;
use App\Http\Utils\SharedCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Objects\Answers;
use App\Http\Objects\FormData;

use App\Http\Utils\ErrorsLogger;
use Illuminate\Http\Request;

final class ResultsController extends Controller
{
    private const INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE = 'Set Content Type header to application/json.';
    private const EMPTY_DATA_EXCEPTION_MESSAGE = 'No valid data provided.';
    private const INVALID_RESULTS_HASH_EXCEPTION_MESSAGE = 'Invalid results hash.';
    private const EXCEPTION_OR_ERROR_PATTERN = 'Exception or error. Message: %s. File: %s. Line: %s. Answers: %s.';
    private const INTERNAL_ERROR_MESSAGE = 'Internal error occured.';
    private const NO_RESULTS_FOR_HASH_ERROR_MESSAGE = 'Could not get results for provided hash.';

    private const ONTAP_RESULTS_HASH_PATTERN = '%s_USER';

    /**
     * @param Request $request
     * @param ErrorsLogger $errorsLogger
     * @param SharedCache $sharedCache
     *
     * @return Response
     * @throws \JsonException
     */
    public function resultsAction( Request $request, ErrorsLogger $errorsLogger, SharedCache $sharedCache ): Response
    {
        if ( $request->header( 'Content-type' ) !== 'application/json' ) {
            $errorsLogger->log( self::INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE );

            return \response( self::INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE, Response::HTTP_BAD_REQUEST );
        }

        $requestData = $request->input();
        if ( $requestData === null || empty( $requestData['answers'] ) ) {
            $errorsLogger->log( self::EMPTY_DATA_EXCEPTION_MESSAGE );

            return \response( self::EMPTY_DATA_EXCEPTION_MESSAGE, Response::HTTP_BAD_REQUEST );
        }

        try {
            $formData = new FormData( new Answers(), $requestData );
        } catch ( \InvalidArgumentException $ex ) {
            $errorsLogger->log( self::INVALID_RESULTS_HASH_EXCEPTION_MESSAGE );

            return \response( self::INVALID_RESULTS_HASH_EXCEPTION_MESSAGE, Response::HTTP_BAD_REQUEST );
        }

        try {
            $inputAnswers = \resolve( 'QuestionsService' )->validateInput( $requestData );
        } catch ( \UnexpectedValueException $ex ) {
            $errorsLogger->log( $ex->getMessage() );

            return \response( $ex->getMessage(), Response::HTTP_BAD_REQUEST );
        }

        try {
            $beerData = ( \resolve( 'AlgorithmService' ) )
                ->createBeerData( $inputAnswers, $formData );
        } catch ( \Exception $ex ) {
            $errorMessage = \sprintf(
                self::EXCEPTION_OR_ERROR_PATTERN,
                $ex->getMessage(),
                $ex->getFile(),
                $ex->getLine(),
                \implode( ',', $inputAnswers )
            );
            $errorsLogger->log( $errorMessage );
            return \response( self::INTERNAL_ERROR_MESSAGE, Response::HTTP_INTERNAL_SERVER_ERROR );
        }

        if ( $formData->addToNewsletterList() && $formData->getEmail() !== null ) {
            \resolve( 'NewsletterService' )->addToNewsletterList( $formData->getEmail() );
        }

        \resolve( 'AnswersLoggerService' )->logAnswers( $formData, $inputAnswers, $beerData );

        // save it to cache for an hour for OnTap
        $cacheKey = SimpleResultsService::RESULTS_CACHE_KEY_PREFIX . $formData->getResultsHash();
        $jsonResults = \json_encode(
            $beerData->toArray(), \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE
        );
        $sharedCache->set( $cacheKey, $jsonResults );

        return \response()
            ->json( $beerData->toArray(), JsonResponse::HTTP_OK, [], \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE );
    }

    public function resultsByResultsHashAction( string $resultsHash ): Response
    {
        $resulsJson = \resolve( 'SimpleResultsService' )
            ->getResultsByResultsHash( $resultsHash ); //todo: may be stored in cache for an hour?

        if ( $resulsJson === null ) {
            return \response( self::NO_RESULTS_FOR_HASH_ERROR_MESSAGE, Response::HTTP_BAD_REQUEST );
        }

        return \response( $resulsJson )->header( 'Content-Type', 'application/json' );
    }
}
