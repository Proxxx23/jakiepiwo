<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickingAlgorithm extends Controller
{

	/*
	* Array zawiera pary 'odpowiedź' => id_piw z bazy do zaliczenia  w przypadku wyboru tej odpowiedzi
	*/
	public $to_include[1] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[2] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[3] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[4] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[5] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[6] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[7] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[8] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[9] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[10] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[11] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[12] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[13] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[14] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	public $to_include[15] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));

	public $included_ids = array();
    
    public function includeBeerIds(string $answers) : void {

    	$answers_decoded = json_decode($answers);

    	foreach ($answers_decoded AS $number => $answer) {
    		foreach ($to_include[$number] AS $yesno) {
    			if (in_array($answer, $yesno)) {
    				foreach ($yesno AS $id) {
    					if (!in_array($included_ids, $id)) {
    						$this->included_ids[$id] = 1;
    					} else {
    						$this->included_ids[$id]++;
    					}
    				}
    			}
    		}
    	}
    }


    public function logStyles(string $name, string $email) :bool {

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
