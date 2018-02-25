<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickingAlgorithm extends Controller
{

	private $choosen_style = array();

	/*
	* Array zawiera pary 'odpowiedź' => id_piw z bazy do wykluczenia w przypadku wyboru tej odpowiedzi
	*/
	public $to_exclude_1 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_2 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_3 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_4 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_5 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_6 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_7 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_8 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_9 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_10 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_11 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_12 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_13 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_14 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude_15 = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
    
    public function chooseStyles($answers) {

    	$answers_decoded = json_decode($answers);
    	// Obrabnia JSON-a i kolejno wywala style na podstawie kolejnych odpowiedzi

    }

    public function logStyles($name, $email) {

    	$insert_styles = DB::insert("INSERT INTO `styles_log` (username, e_mail, style_1, style_2, style_3)
    											VALUES
    										('{name}', '{$email}', '{$this->choosen_style[0]}', '{$this->choosen_style[1]}', '{$this->choosen_style[2]}')");

    	if ($insert_styles) {
    		return true;
    	} else {
    		return false;
    	}

    }


}
