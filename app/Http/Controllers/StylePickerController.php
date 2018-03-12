<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ValidationController as Validation;
use App\Http\Controllers\PickingAlgorithm as Algorithm;
use App\Traits\Questions as Questions;
use App\Styles;
use \DrewM\MailChimp\MailChimp;
use Mail;

class StylePickerController extends Controller
{

    public $error_cnt = 0;
	public $error_msg = array();
	public $JSON_answers = '';
	public $mostly_picked = array();

    /*
    * Show all the questions
    * return: view
    */
    public function showQuestions(bool $errors = false) {

    	//die('Wprowadzam zmiany w pytaniach i algorytmie. Beta będzie włączona ponownie około 20:30');

        if ($errors === true) {
    		return view('index', ['questions' => Questions::$questions,	'mostly_picked' => $this->getMostPickedStyles(), 'lastvisit_name' => $this->getUsername(), 'errors' => $this->error_msg, 'errors_count' => $this->error_cnt]);
    	} else {
    		return view('index', ['questions' => Questions::$questions,	'mostly_picked' => $this->getMostPickedStyles(), 'lastvisit_name' => $this->getUsername(), 'errors' => '', 'errors_count' => 0]);
    	}

    }

    /*
    * Prepares a TPL for an e-mail
    * return: $mailTPL string
    */
    private function prepareEmailTemplate() : string {

    	$mailTPL = '';
    	$mailTPL .= '';
    	$mailTPL .= '';

    	return $mailTPL;

    }

    /*
    * Sends an e-mail if user wants to
    * return: bool
    */
    public function sendEmail() : bool {

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
    private function addEmailToNewsletterList($email) : void {

    	$MailChimp = new MailChimp('d42a6395b596459d1e2c358525a019b7-us3');
    	$list_id = 'e51bd39480';

		$result = $MailChimp->post("lists/$list_id/members", [
			'email_address' => $email,
			'status'        => 'subscribed',
		]);

    }

    /*
    * Adds email to a Mailchimp list
    * return $set_newsletter integer
    */
    private function setNewsletter(?string $email) : int {

    	$validation = new Validation();

    	if ($validation->validateEmail($email)) {
    		$this->addEmailToNewsletterList($email);
    		$set_newsletter = 1;
    	} else {
    		$set_newsletter = 0;
    	}

    	return $set_newsletter;

    }

    public function prepareAnswers() : bool {

    	$answers = array();
    	$validation = new Validation();

    	for ($i = 1; $i <= count(Questions::$questions); $i++) {
    		
    		// Opcjonalne nie są sprawdzane w ten sposób
    		if ($i <= 13) {
	    		if (is_null($_POST['answer-'.$i.''])) { 
	    			$this->logError('Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!');
	    		}
    		}

    		if (isset($_POST['answer-'.$i.''])) {
    			if (!$validation->validateSimpleAnswer($_POST['answer-'.$i.'']) && (Questions::$questions[$i]['type'] != 1)) {
    				$this->logError('Problem z walidacją niektórych pól formularza!', true);
    			}
    		}

    		$answers = $this->array_push_assoc($answers, $i, $_POST['answer-'.$i.'']);

    	}

		$this->JSON_answers = json_encode($answers); //JSON $_POST answers

		if ($this->JSON_answers !== '') {
			return true;
		} else {
			$this->logError('Brak odpowiedzi na pytania!');
			return false;
		}

		return true;

    }

    // Wstawia do bazy odpowiedzi użytkownika
    // Rozdzielić na osobną funkcjędo bazy, osobną do wywołania innych rzeczy
    public function mix() {

    	$validation = new Validation();

    	$name = $_POST['username'] ?? 'Gość';
    	($validation->validateEmail($_POST['email'])) ? $email = $_POST['email'] : $email = '';
    	($_POST['newsletter']) ? $newsletter = 1 : $newsletter = 0;

    	$this->prepareAnswers();

    	if (empty($this->error_msg)) {
    		$insert_answers = DB::insert("INSERT INTO `user_answers` (name, e_mail, newsletter, answers, created_at) 
    										VALUES 
    										(?, ?, ?, ?, ?)",
    									[$name, $email, $newsletter, $this->JSON_answers, NOW()]);

    		if ($insert_answers === true) {

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
				return $algorithm->includeBeerIds($this->JSON_answers, $name, $email, $newsletter);

    		} else {
    			$this->logError('Błąd połączenia z bazą danych!', true);
    			return $this->showQuestions(true);
    		}
    	} else {
    		return $this->showQuestions(true);
    	}
    }

    // Pokazuje style z ostatniej wizyty
    public function getRecentlyPickedStyles() {

    	if ($_SERVER['REMOTE_ADDR']) {
    		$q = DB::select("SELECT * FROM beers_logs WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY created_at DESC LIMIT 1;");
    	}

    }

    /*
    * Selects necessary info about choosen beers from database
    * TODO: Polski Kraft API
    */
    public function getDetailedInfoAboutStyle(integer $beer_ids) : object {

    	$beer_ids = explode(',', $beer_ids);
    	$style_info = DB::select("SELECT * FROM `beer_flavours` WHERE id IN ('".$beer_ids."')");

    	return $style_info;

    }

    /**
    * Takes 3 mostly picked styles
    */
    public function getMostPickedStyles() : ?array {

		$mostly_picked = DB::select('SELECT COUNT(`s`.`style_take`) AS `wybrano_razy`, `b`.`name`, `b`.`name2`, `b`.`name_pl` FROM `styles_logs` s INNER JOIN `beers` b ON `b`.`id` = `s`.`style_take` GROUP BY `s`.`style_take` ORDER BY `wybrano_razy` DESC LIMIT 3');

    	return $mostly_picked;

    }

    /**
    * Gets name of an user using IP address
    */
    public function getUsername() : ?string {

    	$last_visit = DB::select('SELECT username FROM styles_logs WHERE ip_address = "'.$_SERVER['REMOTE_ADDR'].'" ORDER BY created_at DESC LIMIT 1');

    	if ($last_visit) {
			$v = get_object_vars($last_visit[0]);
			return $v['username'];
		} else {
			return null;
		}

    }

    /**
    * Gets 3 styles from last user's visit
    * TODO
    */
    public function getUserLastVisitStyles() {

    	$last_visit_styles = DB::select('SELECT s.style_take, b.name FROM styles_logs s INNER JOIN beers b ON s.style_take = b.id WHERE s.ip_address = "'.$_SERVER['REMOTE_ADDR'].'" ORDER BY created_at DESC LIMIT 3');

    	// if (!empty($last_visit_styles)) {
    	// 	for ($i = 0; $i <= count($last_visit_styles); $i++) {
    	// 		$last_styles[] = get_object_vars($last_visit_styles[$i]);
    	// 	}
    	// 	var_dump($last_styles);
    	// }

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
    	array_push($this->error_msg, $message);
    	$this->error_cnt++;

    }


}
