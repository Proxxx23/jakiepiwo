<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Objects\Options;
use App\Http\Objects\User;
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
    /**
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */
    public function presentStyles( Request $request ): string
    {
        if ( $request->header('Content-type') !== 'application/json' ) {
            throw new \UnexpectedValueException( 'Set Content Type header to application/json' );
        }

        $requestData = $request->input();
        if ( $requestData === null || empty( $requestData['answers'] ) ) {
            throw new \UnexpectedValueException( 'No vaid data provided.' );
        }

        $user = new User( new Options() );
        $user->setUsername( $requestData['username'] ?? null );

        $emailIsValid = EmailUtils::isValid( $requestData['email'] ?? null );
        if ( $emailIsValid ) {
            $user->setEmail( $requestData['email'] );
        }

        $user->setSendEmail( $requestData['sendEmail'] ?? false );
        $user->setAddToNewsletterList( $requestData['newsletter'] ?? false );

        $answers = ( new QuestionsService( new QuestionsRepository() ) )
            ->validateInput( $requestData );

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
                    $user->getUsername(),
                    $user->getEmail(),
                    $user->getAddToNewsletterList(),
                    \json_encode( $answers, JSON_UNESCAPED_UNICODE ),
                    now(),
                ]
            );
        } catch ( \Exception $e ) {
            LogService::logError( $e->getMessage() );
        }

        $mailService = new MailService();
        $userEmail = $user->getEmail();
        if ( $emailIsValid && $userEmail !== null && $user->getSendEmail() ) {
            $mailService->sendEmail( $userEmail );
        }

        $newsletterService = new NewsletterService(
            new NewsletterRepository( new MailChimp( config( 'mail.mailchimpApiKey' ) ) )
        );

        if ( $emailIsValid && $user->getAddToNewsletterList() ) {
            $newsletterService->addToNewsletterList( $userEmail );
        }

        return ( new AlgorithmService( new ScoringRepository() ) )
            ->fetchProposedStyles( $answers, $user );
    }
}
