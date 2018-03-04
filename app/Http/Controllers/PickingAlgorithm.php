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
	public $to_include1 = array('tak' => '1, 2, 3, 4', 'nie' => '5, 6, 7, 8');
	public $to_include2 = array('tak' => '2, 4, 6, 8', 'nie' => '1, 3, 5, 7');
	public $to_include3 = array('tak' => '3, 4, 6, 7', 'nie' => '1, 2, 5, 8');
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
	public $style_to_take = array();
	public $style_to_avoid = array();

	public CONST STYLES_TO_PICK = 3; // Eventually change to user's decision
    
    /**
    * 
    *
    */
    public function includeBeerIds(string $answers, string $name, string $email, $newsletter) {

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

    	// TODO: Check if there are at least 3 styles
    	// If no - make extra Draw
    	$this->chooseStyles($name, $email, $newsletter);

    }

    public function chooseStyles(string $name, string $email, $newsletter) {

    	// Tu musi być jeszcze wywołanie funkcji, która sprawdzi, czy dany styl nie występuje wiele razy i w included i w excluded (bo to nie miałoby sensu, gdyby stout był jednocześnie wybrany i wykluczony)
    	
    	arsort($this->included_ids);
    	arsort($this->excluded_ids);

    	for ($i = 0; $i < self::STYLES_TO_PICK; $i++) {
    		$style_to_take = $this->style_to_take[] = key(array_slice($this->included_ids, $i, 1, true));
    		$buythis[] = DB::select("SELECT * FROM beers WHERE id = :id", ['id' => $style_to_take]);	
    	}

    	for ($i = 0; $i < self::STYLES_TO_PICK; $i++) {
    		$style_to_avoid = $this->style_to_avoid[] = key(array_slice($this->excluded_ids, $i, 1, true));
    		$avoidthis[] = DB::select("SELECT * FROM beers WHERE id = :id", ['id' => $style_to_avoid]);	
    	}

    	// Zapisz wybór do bazy
    	try {
    		$this->logStyles($name, $email, $newsletter);
    	} catch (Exception $e) {
    		//mail('kontakt@piwolucja.pl', 'logStyles Exception', $e->getMessage());
    	}

    	return view('results', ['buythis' => $buythis, 'avoidthis' => $avoidthis, 'username' => $name]);

    }


    public function logStyles(string $name, string $email, $newsletter) : bool {

    	$insert_styles = DB::insert('INSERT INTO `styles_logs` (username, email, newsletter, style_1, style_2, style_3, style_1_avoid, style_2_avoid, style_3_avoid, created_at)
    											VALUES
    								(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
    											[$name, 
    											$email, 
    											$newsletter,
			    								$this->style_to_take[0], 
			    								$this->style_to_take[1], 
			    								$this->style_to_take[2],
			    								$this->style_to_avoid[0],
			    								$this->style_to_avoid[1],
			    								$this->style_to_avoid[2],
			    								NOW()]
			    								);

    	if ($insert_styles) {
    		return true;
    	} else {
    		return false;
    	}

    }


}
