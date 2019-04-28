<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Objects\Options;
use App\Http\Objects\User;
use App\Http\Repositories\NewsletterRepository;
use App\Http\Repositories\QuestionsRepository;
use App\Http\Repositories\ScoringRepository;
use App\Http\Services\MailService;
use App\Http\Services\NewsletterService;
use App\Http\Services\QuestionsService;
use App\Http\Services\UserService;
use App\Http\Utils\ValidationUtils;
use App\Http\Services\AlgorithmService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

class AlgorithmController
{
    /** @var int */
    private $errorsCount = 0;
    /** @var array */
    private $errorMesage = [];

    /**
     * @param Request $request
     *
     * @return View
     * @throws \Exception
     *
     * TODO: Za gruby kontroler
     */
    public function presentStyles( Request $request ): View
    {
        // TODO: DiContainer
        $mailService = new MailService();
        $userService = new UserService();
        $questionsService = new QuestionsService( new QuestionsRepository() );
        $newsletterService = new NewsletterService( new NewsletterRepository() );

        if ( !empty( $this->errorMesage ) ) {
            return view(
                'index', [
                    'questions' => $questionsService->getQuestions(),
                    'lastVisitName' => $userService->getUsername(),
                    'errors' => $this->errorMesage,
                    'errorsCount' => $this->errorsCount,
                ]
            );
        }

        //TODO: To wszystko lecieć będzie z requesta
        $user = new User( new Options() );
        $user->setUsername( $_POST['username'] );

        if ( isset( $_POST['email'] ) && ValidationUtils::emailIsValid( $_POST['email'] ) ) {
            $user->setEmail($_POST['email']);
        }

        if ( !empty( $_POST['newsletter'] ) ) {
            $user->setNewsletterOpt(1);
        }

        // TODO: Obiekt z wypełnianiem pól z JSON-a
        $answers = $questionsService->fetchJsonAnswers( $request );

        $insertAnswers = DB::insert(
            'INSERT INTO `user_answers` 
                                    (name, 
                                     e_mail, 
                                     newsletter, 
                                     answers, 
                                     created_at) 
                                        VALUES 
                                        (?, ?, ?, ?, ?)',
            [
                $user->getUsername(),
                $user->getEmail(),
                $user->getNewsletterOpt(),
                $answers,
                now(),
            ]
        );

        if ( !$insertAnswers ) {
            $this->logError( 'Błąd połączenia z bazą danych!', true );
            return view(
                'index', [
                    'questions' => $questionsService->getQuestions(),
                    'jsonQuestions' => $questionsService->getJsonQuestions(),
                    'lastVisitName' => $userService->getUsername(),
                    'errors' => null,
                    'errorsCount' => 0,
                ]
            );
        }

        $userEmail = $user->getEmail();

        // todo: na sam koniec
        $newsletterService->addToNewsletterList( $userEmail, $user->getNewsletterOpt() );

        if ( $userEmail !== null && !empty( $_POST['sendMeAnEmail'] ) ) {
            $mailService->sendEmail( $userEmail );
        }

        $algorithmService = new AlgorithmService( new ScoringRepository() );

        return $algorithmService->fetchProposedStyles( $answers, $user );
    }

    /**
     * @param string $message
     *
     * TODO: Przebudować na zrzucanie wszystkich błędów w jednym insercie
     * TODO: handling winny sposób
     */
    public function logErrorToDB( string $message ): void
    {
        try {
            DB::insert(
                'INSERT INTO error_logs (error, created_at) VALUES (:error, :created_at)',
                [
                    'error' => $message,
                    'created_at' => now(),
                ]
            );
        } catch ( \Exception $e ) {
            die( $e->getMessage() );
        }
    }

    /**
     * Loguje wszelkie błędy
     *
     * @param string $message
     * @param bool $die
     *
     * TODO: handling w inny sposób - nie w kontrolerze, a przez Exceptiony
     */
    public function logError( string $message, bool $die = false ): void
    {
        if ( $die ) {
            die( $message );
        }

        $this->logErrorToDB( $message );
        $this->errorMesage[] = $message;
        $this->errorsCount++;
    }
}
