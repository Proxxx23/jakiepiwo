<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Repositories\ResultsRepository;
use App\Http\Services\SimpleResultsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Objects\Answers;
use App\Http\Objects\FormData;
use App\Http\Repositories\BeersRepository;
use App\Http\Repositories\ErrorLogsRepository;
use App\Http\Repositories\NewsletterRepository;
use App\Http\Repositories\PolskiKraftRepository;
use App\Http\Repositories\QuestionsRepository;
use App\Http\Repositories\ScoringRepository;
use App\Http\Repositories\StylesLogsRepository;
use App\Http\Repositories\UserAnswersRepository;
use App\Http\Services\AnswersLoggerService;
use App\Http\Services\NewsletterService;
use App\Http\Services\QuestionsService;
use App\Http\Services\AlgorithmService;

use App\Http\Utils\Dictionary;
use App\Http\Utils\ErrorsLogger;
use DrewM\MailChimp\MailChimp;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

final class ResultsController extends Controller
{
    private const INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE = 'Set Content Type header to application/json.';
    private const EMPTY_DATA_EXCEPTION_MESSAGE = 'No valid data provided.';
    private const INVALID_RESULTS_HASH_EXCEPTION_MESSAGE = 'Invalid results hash.';
    private const EXCEPTION_OR_ERROR_PATTERN = 'Exception or error. Message: %s. File: %s. Line: %s';
    private const INTERNAL_ERROR_MESSAGE = 'Internal error occured.';
    private const NO_RESULTS_FOR_HASH_ERROR_MESSAGE = 'Could not get results for provided hash.';
    private const APPLICATION_JSON_HEADER = 'application/json';

    private const DEFAULT_CACHE_TTL = 900;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function resultsAction( Request $request ): JsonResponse
    {
        $logger = ( new ErrorsLogger( new ErrorLogsRepository() ) );
        if ( $request->header( 'Content-type' ) !== self::APPLICATION_JSON_HEADER ) {
            $logger->logError( self::INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE );
            return \response()->json(
                [
                    'messsage' => self::INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE,
                ], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $requestData = $request->input();
        if ( $requestData === null || empty( $requestData['answers'] ) ) {
            $logger->logError( self::EMPTY_DATA_EXCEPTION_MESSAGE );
            return \response()->json(
                [
                    'messsage' => self::EMPTY_DATA_EXCEPTION_MESSAGE,
                ], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $formData = new FormData( new Answers(), $requestData );
        } catch ( \InvalidArgumentException $ex ) {
            $logger->logError( self::INVALID_RESULTS_HASH_EXCEPTION_MESSAGE );
            return \response()->json(
                [
                    'messsage' => self::INVALID_RESULTS_HASH_EXCEPTION_MESSAGE,
                ], JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $answers = ( new QuestionsService( new QuestionsRepository() ) )->validateInput( $requestData );

        $httpClient = new Client();

        try {
            $beerData = ( new AlgorithmService(
                new ScoringRepository(),
                new PolskiKraftRepository(
                    new Dictionary(),
                    new FilesystemAdapter( '', self::DEFAULT_CACHE_TTL ),
                    $httpClient
                ),
                new StylesLogsRepository(),
                new BeersRepository(),
                new ErrorsLogger( new ErrorLogsRepository() )
            ) )
                ->createBeerData( $answers, $formData );
        } catch ( \Exception $ex ) {
            $errorMessage = \sprintf(
                self::EXCEPTION_OR_ERROR_PATTERN,
                $ex->getMessage(),
                $ex->getFile(),
                $ex->getLine()
            );
            var_dump($errorMessage);die();
            $logger->logError( $errorMessage );
            return \response()->json(
                [
                    'messsage' => self::INTERNAL_ERROR_MESSAGE,
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ( $formData->addToNewsletterList() && $formData->getEmail() !== null ) {
                $newsletterService = new NewsletterService(
                    new NewsletterRepository( new MailChimp( \config( 'mail.mailchimpApiKey' ) ) )
                );
                $newsletterService->addToNewsletterList( $formData->getEmail() );
        }

        ( new AnswersLoggerService( new UserAnswersRepository() ) )->logAnswers( $formData, $answers, $beerData );

        return \response()
            ->json( $beerData->toArray(), JsonResponse::HTTP_OK, [], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE );
    }

    public function resultsByResultsHashAction( string $resultsHash ): Response
    {
        $service = new SimpleResultsService( new ResultsRepository() );
        $resulsJson = $service->getResultsByResultsHash( $resultsHash ); //todo: may be stored in cache for an hour?

        if ( $resulsJson === null ) {
            return \response()->json(
                [
                    'messsage' => self::NO_RESULTS_FOR_HASH_ERROR_MESSAGE,
                ], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return \response( $resulsJson )->header( 'Content-Type', 'application/json' );
    }
}
