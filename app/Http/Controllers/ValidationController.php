<?php

namespace App\Http\Controllers;

require_once('C:\xampp\htdocs\jakiepiwomamkupic\app\Questions.php');

use Illuminate\Http\Request;
use App\Styles;
use App\Traits\Questions as Questions;

class ValidationController extends Controller
{

	/*
	* Waliduje adres e-mail użytkownika
	* @param: $email
	*/
    public function validateEmail() : string {

    	$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    	if (!preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)) {

  			// Error

    	} else {
    		return $email;
    	}

    }

	/*
	* Waliduje odpowiedzi tak/nie
	* @param: $answers array
	*/
    public function validateSimpleAnswer(string $answer) : bool {

    	if (!in_array($answer, array('tak', 'nie'))) {
    		return false;
    	} else {
    		return true;
    	}

    }

    /*
	* Waliduje odpowiedzi skali
	* @param: $answers array
	*/
    public function validateAnswers(array $answers) : bool {

    	if (!in_array($answer, Questions::$questions[6]['answers']) || 
    		!in_array($answer, Questions::$questions[8]['answers'])) {
    		return false;
    	} else {
    		return true;
    	}

    }

    /*
	* Waliduje dodatkowe odpowiedzi
	* @param: $answers array
	*/
    public function validateAdditionalAnswers(array $answers) : bool {


    }


}
