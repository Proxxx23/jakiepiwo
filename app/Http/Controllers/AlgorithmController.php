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
use App\Http\Utils\ValidationUtils;
use App\Http\Services\AlgorithmService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlgorithmController
{
    /**
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */
    public function presentStyles( Request $request ): string
    {
        // TODO: DiContainer
        $mailService = new MailService();

        $questionsService = new QuestionsService( new QuestionsRepository() );
        $newsletterService = new NewsletterService( new NewsletterRepository() );

        $requestData = $request->input();
        if ( empty( $requestData ) || empty( $requestData['answers'] ) ) {
            throw new \UnexpectedValueException( 'No vaid data provided.' );
        }

        $answers = $questionsService->validateInput( $requestData );
        $email = $requestData['email'] ?? null;

        $user = new User( new Options() );
        $user->setUsername( $requestData['username'] ?? null );

        $emailIsValid = ValidationUtils::emailIsValid( $email );

        if ( $email !== null && $emailIsValid ) {
            $user->setEmail( $requestData['email'] );
        }

        $user->setSendEmail( $requestData['sendEmail'] ?? false );
        $user->setAddToNewsletterList( $requestData['newsletter'] ?? false );

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

        $userEmail = $user->getEmail();

        if ( $userEmail !== null && $user->getSendEmail() === true && $emailIsValid ) {
            $mailService->sendEmail( $userEmail );
        }

        if ( $user->getAddToNewsletterList() && $emailIsValid ) {
            $newsletterService->addToNewsletterList( $userEmail );
        }

        $algorithmService = new AlgorithmService( new ScoringRepository() );

        return $algorithmService->fetchProposedStyles( $answers, $user );
    }
}
