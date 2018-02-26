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
	public $to_exclude[1] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[2] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[3] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[4] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[5] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[6] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[7] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[8] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[9] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[10] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[11] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[12] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[13] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[14] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_exclude[15] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));

	public $excluded_ids = array();
    
    public function excludeBeerIds(string $answers) : void {

    	$answers_decoded = json_decode($answers);
    	// Obrabnia JSON-a i kolejno wywala style na podstawie kolejnych odpowiedzi

    	foreach ($answers_decoded AS $number => $answer) {
    		foreach ($to_exclude[$number] AS $yesno => $ids_to_exclude) {
    			if (in_array($answer, $yesno)) {
    				foreach ($ids_to_exclude AS $id) {
    					if (!in_array($excluded_ids, $id)) {
    						$this->excluded_ids = array_push($this->excluded_ids, $id);
    					}
    				}
    			}
    		}
    	}
    }

    // Losowanie na podstawie wagi odpowiedzi
    public function extraDraw() : void {

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
