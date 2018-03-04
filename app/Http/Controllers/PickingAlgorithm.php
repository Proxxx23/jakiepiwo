<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickingAlgorithm extends Controller
{

	/*
	* Array zawiera pary 'odpowiedź' => id_piw z bazy do zaliczenia  w przypadku wyboru tej odpowiedzi
	*/
	public $to_include1 = array('tak' => '1, 4, 5, 8', 'nie' => '2, 3, 6, 7');
	public $to_include2 = array('tak' => '1, 4, 5, 8', 'nie' => '2, 3, 6, 7');
	public $to_include3 = array('tak' => '1, 4, 5, 8', 'nie' => '2, 3, 6, 7');
	// public $to_include[4] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[5] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[6] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[7] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[8] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[9] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[10] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[11] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[12] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[13] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[14] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));
	// public $to_include[15] = array('TAK' => array(1, 4, 5, 8), 'NIE' => array(2, 3, 6, 7));

	public $included_ids = array(); // Beer IDs to include
	public $excluded_ids = array(); // Excluded beer IDs
	public $choosen_style = array();
    
    /**
    * 
    *
    */
    public function includeBeerIds(string $answers) {

    	$answers_decoded = json_decode($answers);

    	foreach ($answers_decoded AS $number => $answer) {
	    	foreach ($this->{'to_include'.$number} AS $yesno => $ids) {

	    		$ids_exploded = explode(', ', $ids);

	    			if ($answer != $yesno) { 
	    				continue; 
	    			}

	    				if ($answer == 'tak') {
	    					foreach ($ids_exploded AS $value) {
	    						if (!empty($this->included_ids[$value])) {
									$this->included_ids[$value]++;
								} else {
									$this->included_ids[$value] = 1;
								}	
	    					}
	    				} else {
	    					foreach ($ids_exploded AS $value) {
								if (!empty($this->excluded_ids[$value])) {
									$this->excluded_ids[$value]++;
								} else {
									$this->excluded_ids[$value] = 1;
								}	
	    					}
	    				}
	    	}
    	}
    }

    public function chooseStyles() {

    	// Tu musi być jeszcze wywołanie funkcji, która sprawdzi, czy damy styl nie występuje wiele razy i w included i w excluded (bo to nie miałoby sensu, gdyby stout był jednocześnie wybrany i wykluczony)
    	
    	arsort($this->included_ids);
    	arsort($this->excluded_ids);

    }


    public function logStyles(string $name, string $email) : bool {

    	$this->chooseStyles();

    	$insert_styles = DB::insert('INSERT INTO `styles_logs` (username, email, style_1, style_2, style_3, created_at)
    											VALUES
    								(?, ?, ?, ?, ?, ?)', 
    											[$name, 
    											$email, 
			    								$this->choosen_style[0], 
			    								$this->choosen_style[1], 
			    								$this->choosen_style[2],
			    								NOW()]
			    								);

    	if ($insert_styles) {
    		return true;
    	} else {
    		return false;
    	}

    }


}
