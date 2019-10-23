<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Exceptions\InvalidContentTypeException;
use App\Http\Objects\Answers;
use App\Http\Objects\UserOptions;
use App\Http\Repositories\NewsletterRepository;
use App\Http\Repositories\QuestionsRepository;
use App\Http\Repositories\ScoringRepository;
use App\Http\Services\LogService;
use App\Http\Services\MailService;
use App\Http\Services\NewsletterService;
use App\Http\Services\QuestionsService;
use App\Http\Services\AlgorithmService;
use App\Http\Utils\EmailUtils;

use DrewM\MailChimp\MailChimp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class AlgorithmController
{
    private const INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE = 'Set Content Type header to application/json.';
    private const EMPTY_DATA_EXCEPTION_MESSAGE = 'No valid data provided.';

    /**
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */
    public function presentStyles( Request $request ): string
    {
        if ( $request->header( 'Content-type' ) !== 'application/json' ) {
            throw new InvalidContentTypeException( self::INVALID_CONTENT_TYPE_EXCEPTION_MESSAGE );
        }

        $requestData = $request->input();
        if ( $requestData === null || empty( $requestData['answers'] ) ) {
            throw new \UnexpectedValueException( self::EMPTY_DATA_EXCEPTION_MESSAGE );
        }

        // todo: normalize from array?
        $userOptions = ( new UserOptions( new Answers() ) )
            ->setUsername( $requestData['username'] )
            ->setEmail( $requestData['email'] )
            ->setSendEmail( $requestData['sendEmail'] )
            ->setAddToNewsletterList( $requestData['newsletter'] );

        $answers = ( new QuestionsService( new QuestionsRepository() ) )
            ->validateInput( $requestData );

        //todo: repo, service
        try {
            DB::insert(
                'INSERT INTO `user_answers` 
                                    (`name`, 
                                     `e_mail`, 
                                     `newsletter`, 
                                     `answers`, 
                                     `created_at`) 
                                        VALUES 
                                        (?, ?, ?, ?, ?)',
                [
                    $userOptions->getUsername(),
                    $userOptions->getEmail(),
                    $userOptions->getAddToNewsletterList(),
                    \json_encode( $answers, JSON_UNESCAPED_UNICODE ),
                    now(),
                ]
            );
        } catch ( \Exception $e ) {
            LogService::logError( $e->getMessage() );
        }

        $mailService = new MailService();
        $userEmail = $userOptions->getEmail();
        if ( $userEmail !== null && $userOptions->getSendEmail() ) {
            $mailService->sendEmail( $userEmail );
        }

        $newsletterService = new NewsletterService(
            new NewsletterRepository( new MailChimp( config( 'mail.mailchimpApiKey' ) ) )
        );

        if ( $userOptions->getAddToNewsletterList() ) {
            $newsletterService->addToNewsletterList( $userEmail );
        }

        return ( new AlgorithmService( new ScoringRepository() ) )
            ->fetchProposedStyles( $answers, $userOptions );
    }
}
