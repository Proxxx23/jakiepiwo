<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use UnexpectedValueException;
use Exception;
use App\Exceptions\InvalidContentTypeException;
use App\Http\Objects\Answers;
use App\Http\Objects\FormData;
use App\Http\Repositories\BeersRepository;
use App\Http\Repositories\ErrorLogsRepository;
use App\Http\Repositories\NewsletterRepository;
use App\Http\Repositories\OnTapRepository;
use App\Http\Repositories\BeerInfoHelper;
use App\Http\Repositories\QuestionsRepository;
use App\Http\Repositories\ScoringRepository;
use App\Http\Repositories\StylesLogsRepository;
use App\Http\Repositories\UserAnswersRepository;
use App\Http\Services\AnswersLoggerService;
use App\Http\Services\MailService;
use App\Http\Services\NewsletterService;
use App\Http\Services\QuestionsService;
use App\Http\Services\AlgorithmService;

use App\Http\Utils\Dictionary;
use App\Http\Utils\ErrorsLogger;
use DrewM\MailChimp\MailChimp;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

final class AlgorithmController
{
    private const INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE = 'Set Content Type header to application/json.';
    private const EMPTY_DATA_EXCEPTION_MESSAGE = 'No valid data provided.';
    private const APPLICATION_JSON_HEADER = 'application/json';

    /**
     * @param Request $request
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function handle( Request $request ): string
    {
        if ( \stripos( $request->header( 'Content-type' ), self::APPLICATION_JSON_HEADER ) === false ) {
            throw new InvalidContentTypeException( self::INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE );
        }

        $requestData = $request->input();
        if ( $requestData === null || empty( $requestData['answers'] ) ) {
            throw new UnexpectedValueException( self::EMPTY_DATA_EXCEPTION_MESSAGE );
        }

        $formData = new FormData( new Answers(), $requestData );
        $answers = ( new QuestionsService( new QuestionsRepository() ) )->validateInput( $requestData );

        //todo: one service/repo - strategy?
        try {
            ( new AnswersLoggerService( new UserAnswersRepository() ) )->logAnswers( $formData, $answers );
        } catch ( Exception $e ) {
            ( new ErrorsLogger( new ErrorLogsRepository() ) )->logError( $e->getMessage() );
        }

        $proposedStyles = ( new AlgorithmService(
            new ScoringRepository(),
            new BeerInfoHelper( new Dictionary(), new OnTapRepository( new Client(), 'Gdańsk' ) ) ,
            new StylesLogsRepository(),
            new BeersRepository(),
            new ErrorsLogger( new ErrorLogsRepository() )))
                ->createBeerData( $answers, $formData );

        if ( $formData->hasEmail() && $formData->sendEmail() ) {
            //todo: any info about mail has been sent
            ( new MailService() )->sendEmail( $proposedStyles, $formData->getUsername(), $formData->getEmail() );
        }

        if ( $formData->addToNewsletterList() && $formData->getEmail() !== null ) {
            $newsletterService = new NewsletterService(
                new NewsletterRepository(new MailChimp( config( 'mail.mailchimpApiKey' )) )
            );
            $newsletterService->addToNewsletterList( $formData->getEmail() );
        }

        return $proposedStyles->toJson();
    }
}
