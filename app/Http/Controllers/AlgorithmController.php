<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Exceptions\InvalidContentTypeException;
use App\Http\Objects\Answers;
use App\Http\Objects\FormInput;
use App\Http\Repositories\NewsletterRepository;
use App\Http\Repositories\PolskiKraftRepository;
use App\Http\Repositories\QuestionsRepository;
use App\Http\Repositories\ScoringRepository;
use App\Http\Repositories\UserAnswersRepository;
use App\Http\Services\DatabaseLoggerService;
use App\Http\Services\LogService;
use App\Http\Services\MailService;
use App\Http\Services\NewsletterService;
use App\Http\Services\QuestionsService;
use App\Http\Services\AlgorithmService;

use App\Http\Utils\Dictionary;
use DrewM\MailChimp\MailChimp;
use Illuminate\Http\Request;

final class AlgorithmController
{
    private const INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE = 'Set Content Type header to application/json.';
    private const EMPTY_DATA_EXCEPTION_MESSAGE = 'No valid data provided.';
    private const APPLICATION_JSON_HEADER = 'application/json';

    /**
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */
    public function handle( Request $request ): string
    {
        if ( \stripos( $request->header( 'Content-type' ), self::APPLICATION_JSON_HEADER ) === false ) {
            throw new InvalidContentTypeException( self::INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE );
        }

        $requestData = $request->input();
        if ( $requestData === null || empty( $requestData['answers'] ) ) {
            throw new \UnexpectedValueException( self::EMPTY_DATA_EXCEPTION_MESSAGE );
        }

        $formInput = new FormInput( new Answers(), $requestData );
        $answers = ( new QuestionsService( new QuestionsRepository() ) )->validateInput( $requestData );

        try {
            $databaseLoggerService = new DatabaseLoggerService( new UserAnswersRepository() );
            $databaseLoggerService->logAnswers( $formInput, $answers );
        } catch ( \Exception $e ) {
            LogService::logError( $e->getMessage() );
        }

        $mailService = new MailService();
        $userEmail = $formInput->getEmail();
        if ( $userEmail !== null && $formInput->getSendEmail() ) {
            $mailService->sendEmail( $userEmail );
        }

        $newsletterService = new NewsletterService(
            new NewsletterRepository( new MailChimp( config( 'mail.mailchimpApiKey' ) ) )
        );

        if ( $formInput->getAddToNewsletterList() ) {
            $newsletterService->addToNewsletterList( $userEmail );
        }

        return ( new AlgorithmService( new ScoringRepository(), new PolskiKraftRepository( new Dictionary()) ) )
            ->fetchProposedStyles( $answers, $formInput );
    }
}
