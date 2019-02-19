<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Questions;
use http\Env\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ValidationService as Validation;
use App\Http\Controllers\PickingAlgorithm as Algorithm;
use \DrewM\MailChimp\MailChimp;
use Illuminate\View\View;
use Mail;

final class StylePickerController extends Controller
{
    use Questions;

    /** @var int */
    private $errorsCount = 0;
    /** @var array */
    private $errorMesage = [];

    /**
     * Show all the questions
     * @Get('/questions')
     *
     * @param bool $errors
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showQuestions(bool $errors = false)
    {

        // if ($_SERVER['REMOTE_ADDR'] != '89.64.48.176') {
        // 	die('Prace serwisowe nad v 0.6 nightly. Serwis wróci do działania o 20:00.');
        // }

        if ($errors === true) {
            return view('index', [
                'questions' => Questions::$questions,
                'lastvisitName' => $this->getUsername(),
                'errors' => $this->errorMesage,
                'errorsCount' => $this->errorsCount
            ]);
        }

        $jsonQuestions = json_encode(Questions::$jsonReadyQuestions);

        return view('index', [
            'questions' => Questions::$questions,
            'jsonQuestions' => $jsonQuestions,
            'lastvisitName' => $this->getUsername(),
            'errors' => '',
            'errorsCount' => 0
        ]);

    }

    /**
     * Prepares a TPL for an e-mail
     * @return string $mailTPL
     */
    private function prepareEmailTemplate(): string
    {
        $mailTPL = '';
        $mailTPL .= '';
        $mailTPL .= '';

        return $mailTPL;
    }

    /**
     * Sends an e-mail if user wants to
     * @return bool
     */
    public function sendEmail(): bool
    {
        $validation = new Validation();

        $headers = 'From: jakiepiwomamwybrac@piwolucja.pl' . "\r\n" .
            'Reply-To: jakiepiwomamwybrac@piwolucja.pl' . "\r\n";

        $subject = $_POST['username'] . ', oto 3 najlepsze style dla Ciebie!';

        if ($validation->validateEmail()) {
            mail($_POST['email'], $subject, $this->prepareEmailTemplate(), $headers);
            return true;
        }

        $this->logError('Błędny adres e-mail!');

        return false;
    }

    /**
     * @documentation: https://github.com/drewm/mailchimp-api
     *
     * @param string|null $email
     *
     * @throws \Exception
     */
    private function addEmailToNewsletterList(?string $email): void
    {
        $MailChimp = new MailChimp('d42a6395b596459d1e2c358525a019b7-us3');
        $listId = 'e51bd39480';

        $result = $MailChimp->post("lists/$listId/members", [
            'email_address' => $email,
            'status' => 'pending',
        ]);
    }

    /**
     * Adds email to a Mailchimp list
     *
     * @param string|null $email
     * @return bool
     * @throws \Exception
     */
    private function setNewsletter(?string $email): bool
    {
        $validation = new Validation();

        if ($validation->validateEmail()) {
            $this->addEmailToNewsletterList($email);
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     * @return string|null
     */
    protected function fetchJsonAnswers(Request $request): ?string
    {
        $answers = \json_decode($request->getQuery('json'));
        if (empty($answers)) {
            $this->logError('Brak odpowiedzi na pytania!');
            return null;
        }
        $questionsCount = \count(Questions::$questions);
        if ($questionsCount !== \count($answers)) {
            $this->logError('Liczba odpowiedni na pytania nie zgadza się z liczbą pytań!');
            return null;
        }

        for ($i = 1; $i <= $questionsCount; $i++) {
            if (null === $answers[$i]) {
                $this->logError('Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!');
            }
        }

        return $answers;
    }

    /**
     * @Get('/result')
     *
     * @return View
     * @throws \Exception
     */
    public function mix(): View
    {
        $validation = new Validation();

        $name = $_POST['username'] ?? 'Gość';
        $validation->validateEmail() ? $email = $_POST['email'] : $email = null;
        $_POST['newsletter'] ? $newsletter = 1 : $newsletter = 0;

        $answers = $this->fetchJsonAnswers(new Request());

        if (!empty($this->errorMesage)) {
            return $this->showQuestions(true);
        }

        $insertAnswers = DB::insert("INSERT INTO `user_answers` 
                                    (name, 
                                     e_mail, 
                                     newsletter, 
                                     answers, 
                                     created_at) 
                                        VALUES 
                                        (?, ?, ?, ?, ?)",
            [
                $name,
                $email,
                $newsletter,
                $answers,
                NOW()
            ]);

        if (!$insertAnswers) {
            $this->logError('Błąd połączenia z bazą danych!', true);
            return $this->showQuestions(true);
        }

        // Dodaj do listy newsletterowej
        if ($newsletter === 1 && $email === null) {
            $this->logError('Jeżeli chcesz dopisać się do newslettera, musisz podać adres e-mail.');
        } elseif ($newsletter === 1 && $email !== null) {
            $this->setNewsletter($email);
        }

        // Wyślij maila na prośbę
        if ($email !== null && !empty($_POST['sendMeAnEmail'])) {
            $this->sendEmail();
        }

        // Algorytm wybiera piwa
        $algorithm = new Algorithm();

        return $algorithm->includeBeerIds($answers, $name, $email, $newsletter);
    }

    /**
     * Gets name of an user using IP address
     */
    public function getUsername(): ?string
    {
        $lastVisit = DB::select('SELECT `username` FROM `styles_logs` WHERE `ip_address` = "' . $_SERVER['REMOTE_ADDR'] . '" AND `username` != "" ORDER BY `created_at` DESC LIMIT 1');

        if ($lastVisit) {
            $v = \get_object_vars($lastVisit[0]);
            return $v['username'];
        }

        return null;
    }

    /**
     * @param string $message
     *
     * TODO: Przebudować na zrzucanie wszystkich błędów w jednym insercie
     */
    public function logErrorToDB(string $message): void
    {
        try {
            DB::insert("INSERT INTO error_logs (error, created_at) VALUES (:error, :created_at)",
                ['error' => $message, 'created_at' => NOW()]);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Loguje wszelkie błędy
     *
     * @param string $message
     * @param bool $die
     */
    public function logError(string $message, bool $die = false): void
    {
        if ($die) {
            die($message);
        }

        $this->logErrorToDB($message);
        $this->errorMesage[] = $message;
        $this->errorsCount++;
    }
}
