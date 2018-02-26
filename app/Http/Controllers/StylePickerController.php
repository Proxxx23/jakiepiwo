<?php

namespace App\Http\Controllers;

require_once('C:\xampp\htdocs\jakiepiwomamkupic\app\Questions.php');
require_once('C:\xampp\htdocs\jakiepiwomamkupic\app\http\controllers\ValidationController.php');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ValidationController as Validation;
use App\Http\Controllers\PickingAlgorithm as Algorithm;
use App\Traits\Questions as Questions;
use App\Styles;

class StylePickerController extends Controller
{

    public $error_cnt = 0;
	public $error_msg = array();
	public $JSON_answers = '';

    public function __construct() {


    }

    /*
    * Custom functions
    */

    // Github: https://gist.github.com/yeco/412610
    private function array_push_assoc(array $array, $key, $value) : array {

 		$array[$key] = $value;
 		return $array;

	}

	// Prints an output with <pre> styling
	public function printPre($data, bool $die = false) {

      	$output = var_dump($data);

      	echo "<pre>";
      	print_r($output);
      	echo "</pre>";

   		if ($die) {
   			die();
   		}
	}

    /*
    * Show all the questions
    * return: view
    */
    public function showQuestions() {

        return view('index', ['questions' => Questions::$questions, 'accurate_questions' => Questions::$accurate_questions]);

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

    	$subject = $_POST['username'] . ' oto 3 najlepsze style dla Ciebie';

    	if ($validation->validateEmail()) {
    		mail($_POST['email'], $subject, $this->prepareEmailTemplate(), $headers);
    		return true;
    	} else {
    		$this->error('Błędny adres e-mail!');
    		return false;
    	}

    }

    private function setNewsletter() : integer {

    	$validation = new Validation();

    	if ($validation->validateEmail()) {
    		$set_newsletter = 1;
    	} else {
    		$set_newsletter = 0;
    	}

    	// Intagracja z MailChimpem - wywołanie w mix()

    	return $set_newsletter;

    }

    private function prepareAnswers() : ?bool {

    	$answers = array();
    	$validation = new Validation();

    	for ($i = 1; $i <= 15; $i++) {
    		
    		if (is_null($_POST['answer-'.$i.''])) { 
    			$this->error('Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!', true);
    		}

    		if (!$validation->validateSimpleAnswer($_POST['answer-'.$i.'']) && $i != 6 && $i != 8) {
    			$this->error('Problem z walidacją niektórych pól formularza!', true);
    		}

    		$answers = $this->array_push_assoc($answers, 'answer-'.$i, $_POST['answer-'.$i.'']);
    	}

		$this->JSON_answers = json_encode($answers); //JSON $_POST answers
		return true;

    }

    // Wstawia do bazy odpowiedzi użytkownika
    // Rozdzielić na osobną funkcjędo bazy, osobną do wywołania innych rzeczy
    public function mix() {

    	$validation = new Validation();

    	$name = $_POST['username'] ?? 'Gość';
    	$email = $validation->validateEmail($_POST['email']) ?? '';
    	($_POST['newsletter'] === 'Tak') ? $newsletter = 1 : $newsletter = 0;

    	if ($this->prepareAnswers() && empty($this->error_msg)) {
    		$insert_answers = DB::insert("INSERT INTO `user_answers` (name, e_mail, newsletter, answers, created_at) 
    										VALUES 
    									('{$name}', '{$email}', '{$newsletter}', '{$this->JSON_answers}', CURRENT_TIMESTAMP)");

    		if ($insert_answers === true) {

    			if ($_POST['sendMeAnEmail']) { 
    				//$this->sendEmail();
    			}

    			if ($_POST['newsletter'] === 1) {
    				// DOdaj do listy newsletterowej
    				// Mailchimp API
    			}

    			// Wywalamy komuś listę piw
    			$algorithm = new Algorithm();
    			$choosen_styles = $algorithm->chooseStyles($this->JSON_answers);
    			$algorithm->logStyles($name, $email);

    			return view('/result', ['styles' => $choosen_styles]);

    		} else {
    			$this->error('Nie udało się wykonać insertu na bazie!');
    			$this->showErrors();
    		}
    	} else {
    		$this->showErrors();
    	}


    }

    // Pokazuje style z ostatniej wizyty
    public function showRecentlyPickedStyles() {

    }

    // 5 najczęściej wybieranych stylów
    public function showMostPickedStyles() {

    	// Musi być tabela odkładająca wybrane użytkownikom style (zliczanie - jak logi)
    	$mostly_picked = DB::select('SELECT * FROM styles GROUP BY count(id) AS mostlypicked ORDER BY mostlypicked DESC LIMIT 3;');

    	return $mostly_picked;


    }

    private function error(string $message, bool $die) : void {

    	$this->error_msg = array_push($this->error_msg, $message);
    	$this->error_cnt++;

    	if ($die) {
    		die($message);
    	}

    }

    public function showErrors() {
    	if ($this->error_cnt && !empty($this->error_msg)) {
    		return view('index', ['questions' => Questions::$questions, 'accurate_questions' => Questions::$accurate_questions,					'errors' => $this->error_msg, 'errors_count' => $this->error_cnt]);
    	} 
    }


}
