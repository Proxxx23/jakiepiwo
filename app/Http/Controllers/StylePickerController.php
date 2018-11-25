<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ValidationController as Validation;
use App\Http\Controllers\PickingAlgorithm as Algorithm;
use App\Traits\Questions as Questions;
use App\Http\Controllers\PolskiKraft\PolskiKraftAPI AS PKAPI;
use App\Styles;
use \DrewM\MailChimp\MailChimp;
use Mail;

final class StylePickerController extends Controller
{

    private $errorsCound = 0;
	private $errorMesage = array();
	private $JSONAnswers = '';

    /**
    * Show all the questions
    * @Get('/questions')
    */
    public function showQuestions(bool $errors = false) {

    	// if ($_SERVER['REMOTE_ADDR'] != '89.64.48.176') {
    	// 	die('Prace serwisowe nad v 0.6 nightly. Serwis wróci do działania o 20:00.');
    	// }

        if ($errors === true) {
    		return view('index', ['questions' => Questions::$questions, 'lastvisitName' => $this->getUsername(), 'errors' => $this->errorMesage, 'errorsCount' => $this->errorsCound]);
    	} else {
    		return view('index', ['questions' => Questions::$questions, 'lastvisitName' => $this->getUsername(), 'errors' => '', 'errorsCount' => 0]);
    	}

    }

    /**
    * Prepares a TPL for an e-mail
    * @return null|string $mailTPL
    */
    private function prepareEmailTemplate(): string {

    	$mailTPL = '';
    	$mailTPL .= '';
    	$mailTPL .= '';

    	return $mailTPL;

    }

    /**
    * Sends an e-mail if user wants to
    * @return bool
    */
    public function sendEmail(): bool {

    	$validation = new Validation();

    	$headers = 'From: jakiepiwomamwybrac@piwolucja.pl' . "\r\n" .
    	'Reply-To: jakiepiwomamwybrac@piwolucja.pl' . "\r\n";

    	$subject = $_POST['username'] . ', oto 3 najlepsze style dla Ciebie!';

    	if ($validation->validateEmail()) {
    		mail($_POST['email'], $subject, $this->prepareEmailTemplate(), $headers);
    		return true;
    	} else {
    		$this->logError('Błędny adres e-mail!');
    		return false;
    	}

    }

    /**
    * GitHub: https://github.com/drewm/mailchimp-api
    */
    private function addEmailToNewsletterList($email): void {

    	$MailChimp = new MailChimp('d42a6395b596459d1e2c358525a019b7-us3');
    	$listId = 'e51bd39480';

		$result = $MailChimp->post("lists/$listId/members", [
			'email_address' => $email,
			'status'        => 'pending',
		]);

    }

    /**
    * Adds email to a Mailchimp list
    * @return int $setNewsletter
    */
    private function setNewsletter(?string $email): int {

    	$validation = new Validation();

    	if ($validation->validateEmail($email)) {
    		$this->addEmailToNewsletterList($email);
    		return (int)1;
    	} 
    	
        return (int)0;

    }

    public function prepareAnswers(): bool {

    	$answers = array();
    	$validation = new Validation();

    	for ($i = 1; $i <= count(Questions::$questions); $i++) {
    		
	    	if (is_null($_POST['answer-'.$i.''])) { 
	    		$this->logError('Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!');
	    	}

    		$answers = $this->array_push_assoc($answers, $i, $_POST['answer-'.$i.'']);

    	}

		$this->JSONAnswers = json_encode($answers); //JSON $_POST answers

        if ($this->JSONAnswers === '') {
            $this->logError('Brak odpowiedzi na pytania!');
            return false;
        }

		return true;

    }

    /**
    * Wstawia do bazy odpowiedzi użytkownika
    * Rozdzielić na osobną funkcję do bazy, osobną do wywołania innych rzeczy
    * @Get('/result')
    */
    public function mix() {

    	$validation = new Validation();

    	$name = $_POST['username'] ?? 'Gość';
    	($validation->validateEmail($_POST['email'])) ? $email = $_POST['email'] : $email = '';
    	($_POST['newsletter']) ? $newsletter = 1 : $newsletter = 0;

    	$this->prepareAnswers();

    	if (empty($this->errorMesage)) {
    		$insertAnswers = DB::insert("INSERT INTO `user_answers` (name, e_mail, newsletter, answers, created_at) 
    										VALUES 
    										(?, ?, ?, ?, ?)",
    									[$name, $email, $newsletter, $this->JSONAnswers, NOW()]);

    		if ($insertAnswers === true) {

    			// Wyślij maila na prośbę
    			if ($email != '' && isset($_POST['sendMeAnEmail'])) { 
    				$this->sendEmail($email);
    			}

    			// Dodaj do listy newsletterowej
    			if ($newsletter === 1) {
    				if ($email != '') {
    					$this->setNewsletter($email);
    				} else {
    					$this->logError('Jeżeli chcesz dopisać się do newslettera, musisz podać adres e-mail.');
    				}
    			}

    			// Algorytm wybiera piwa
    			$algorithm = new Algorithm();
				return $algorithm->includeBeerIds($this->JSONAnswers, $name, $email, $newsletter);

    		} else {
    			$this->logError('Błąd połączenia z bazą danych!', true);
    			return $this->showQuestions(true);
    		}
    	} else {
    		return $this->showQuestions(true);
    	}
    }


    /**
    * Gets name of an user using IP address
    */
    public function getUsername() : ?string {

    	$lastVisit = DB::select('SELECT `username` FROM `styles_logs` WHERE `ip_address` = "'.$_SERVER['REMOTE_ADDR'].'" AND `username` <> "" ORDER BY `created_at` DESC LIMIT 1');

    	if ($lastVisit) {
			$v = get_object_vars($lastVisit[0]);
			return $v['username'];
		} 

        return null;
		
    }

	/**
    * Zapisuje błędy do bazy
    */
    //TODO: Przebudować na zrzucanie wszystkich błędów w jednym insercie
    public function logErrorToDB(string $message) : void {

    	try {
    		DB::insert("INSERT INTO error_logs (error, created_at) VALUES (:error, :created_at)",
    					['error' => $message, 'created_at' => NOW()]);
    	} catch (Exception $e) {
    		die($e->getMessage());
    	}

    }

    /**
    * Loguje wszelkie błędy
    */
    public function logError(string $message, bool $die = false) : void {

    	if ($die) {
    		die($message);
    	}

    	$this->logErrorToDB($message);
    	array_push($this->errorMesage, $message);
    	$this->errorsCound++;

    }


}
