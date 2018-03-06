<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PickingAlgorithm extends Controller
{

	/*
	* Array zawiera pary 'odpowiedź' => id_piw z bazy do zaliczenia w przypadku wyboru tej odpowiedzi
	*/
	protected $to_include1 = array('tak' => '1, 2, 3, 4', 'nie' => '5, 6, 7, 8');
	protected $to_include2 = array('tak' => '2, 4, 6, 8', 'nie' => '1, 3, 5, 7');
	protected $to_include3 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include4 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	
	// Pytania skali
	protected $to_include5 = array('leciutkie' => '1, 2, 3, 4', 'przeiętne' => '', 'mocne' => '', 'krew czorta' => '');
	protected $to_include7 = array('ledwie wyczuwalną' => '1, 2, 3, 4', 'lekką' => '1, 2, 3, 4', 'zdecydowanie wyczuwalną' => '', 'mocną' => '', 'jestem hopheadem' => '');

	protected $to_include6 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include8 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include9 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include10 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include11 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include12 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include13 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');
	protected $to_include14 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');

	// Extra questions
	protected $extra_to_include1 = array();
	protected $extra_to_include2 = array();
	protected $extra_to_include3 = array();
	protected $extra_to_include4 = array();
	protected $extra_to_include5 = array();

	private $included_ids = array(); // Beer IDs to include
	private $excluded_ids = array(); // Excluded beer IDs
	private $style_to_take = array();
	private $style_to_avoid = array();

	private CONST STYLES_TO_PICK = 3; // Eventually change to user's decision

    /**
    * Randomizes ids in $to_include vars
    */
	private function randomizer() {

		for ($i = 0 ; $i < 4; $i++) {
			if ($i != 3) {
				$this->r .= $r .= mt_rand(1, 20) . ', ';
			} else {
				$this->r .= $r .= mt_rand(1, 20);
			}
		}

		return $r;

	}
    
    /**
    * TODO: Change name of the method
    */
    public function includeBeerIds(string $answers, string $name, string $email, $newsletter) {

    	$answers_decoded = json_decode($answers);

    	foreach ($answers_decoded AS $number => $answer) {
	    	foreach ($this->{'to_include'.$number} AS $yesno => $ids) {

	    		// Switch to randomize ID-s
	    		//$ids = $this->randomizer();

	    		$ids_exploded = explode(', ', $ids);

	    		if ($answer == 'tak' || $answer == 'nie') {
	    			if ($answer != $yesno) { 
	    				continue; 
	    			}
	    		} 
	    			// Pytania skali - dopracować
	    			if ($answer != 'tak' && $answer != 'nie') {
	    				// Osobne wykluczenie na zakresy IBU/alkoholu, brane pod uwagę na samym końcu
	    				foreach ($ids_exploded AS $value) {
	    						if (!empty($this->included_ids[$value])) {
									$this->included_ids[$value]++;
								} else {
									$this->included_ids[$value] = 1;
								}	
	    					}
	    			} elseif ($answer == 'tak') {
	    					foreach ($ids_exploded AS $value) {
	    						if (!empty($this->included_ids[$value])) {
									$this->included_ids[$value]++;
								} else {
									$this->included_ids[$value] = 1;
								}	
	    					}
	    			} elseif ($answer == 'nie') {
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
    	return $this->chooseStyles($name, $email, $newsletter);

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

    	$insert_styles = DB::insert('INSERT INTO `styles_logs` (username, email, newsletter, style_1, style_2, style_3, style_1_avoid, style_2_avoid, style_3_avoid, ip_address, created_at)
    											VALUES
    								(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
    											[$name, 
    											$email, 
    											$newsletter,
			    								$this->style_to_take[0], 
			    								$this->style_to_take[1], 
			    								$this->style_to_take[2],
			    								$this->style_to_avoid[0],
			    								$this->style_to_avoid[1],
			    								$this->style_to_avoid[2],
			    								$_SERVER['REMOTE_ADDR'],
			    								NOW()]
			    								);

    	if ($insert_styles) {
    		return true;
    	} else {
    		return false;
    	}

    }


}
