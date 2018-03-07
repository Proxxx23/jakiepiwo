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
	// Czy smakują Ci lekkie piwa koncernowe dostępne w sklepach?
	protected $to_include1 = array('tak' => '9, 10, 11, 12, 13', 
		'nie' => '5, 6, 7, 8'); // Nie można uzupełniać jako odwrotność tak, ale trzeba z tym uważać (czasem może być ani nie, ani tak)
	// Czy chcesz poznać nowe smaki?
	protected $to_include2 = array('tak' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 15, 16, 19, 20', 'nie' => '1, 3, 5, 7');
	// Czy piłeś już nietypowe piwa? (słabe pytanie)
	protected $to_include3 = array('tak' => '1, 2, 3, 4', 'nie' => '1, 2, 3, 4');

	// TODO: Wykluczamy to, co już znasz?

	// Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego?
	protected $to_include4 = array('coś lekkiego' => '1, 2, 3, 4', 'coś pośrodku' => '1, 2, 3, 4', 'coś złożonego' => '');
	
	// Jak wysoką goryczkę tolerujesz?
	protected $to_include5 = array('ledwie wyczuwalną' => '9, 14, 15, 16, 17, 18, 19, 20', 'lekką' => '11, 12', 'zdecydowanie wyczuwalną' => '10, 13', 'mocną' => '1, 2, 3, 4, 5, 6, 7, 8', 'jestem hopheadem' => '1, 3, 7');

	// Wolisz jasne czy ciemne? (zmienić na jasne/ciemne)
	protected $to_include6 = array('jasne' => '1, 2, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 20', 'bez znaczenia' => '', 'ciemne' => '3, 4, 12, 18, 19');

	// Raczej słodkie? (zmienić: słodkie/wytrawne)
	// TODO: Więcej opcji (słodkie/bez znaczenia/wytrawne)
	protected $to_include7 = array('słodsze' => '1, 2, 3, 4', ' bez znaczenia' => '', 'wytrawniejsze' => '1, 2, 3, 4');
	// TODO: Jako skala/suwak
	// Klimaty czekoladowe?
	protected $to_include8 = array('tak' => '3, 4, 12, 18', 'nie' => '1, 2, 5, 6, 7, 8, 9, 19, 11, 13, 14, 15, 16, 17, 18, 19, 20');
	// Mocno gazowane?
	protected $to_include9 = array('tak' => '5, 7, 8, 19, 20', 'nie' => '1, 2, 3, 4, 6, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18');
	// Odpowiada Ci palony smak?
	protected $to_include10 = array('tak' => '3, 12', 'nie' => '1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20');
	// Bardziej owocowo?
	protected $to_include11 = array('tak' => '1, 2, 5, 6, 7, 8', 'nie' => '3, 4, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20');
	// Co powiesz na piwo kwaśne?
	protected $to_include12 = array('tak' => '100', 'nie' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 10');
	// Co powiesz na piwo słonawe?
	protected $to_include13 = array('tak' => '100', 'nie' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 10');

	// Extra questions (dym z ogniska/wędzonka) / Islay whisky
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
	private function randomizer() : int {

		for ($i = 0 ; $i < 4; $i++) {
			if ($i != 3) {
				$this->r .= $r .= mt_rand(1, 20) . ', ';
			} else {
				$this->r .= $r .= mt_rand(1, 20);
			}
		}

		return $r;

	}

	private function synergy(int $n, int $multiplier, bool $positive = true) {

		if ($positive) {
			$this->included_ids[$n] *= $multiplier;
		} else {
			$this->excluded_ids[$n] *= $multiplier;
		}

	}

    
    /**
    * TODO: Change name of the method
    */
    public function includeBeerIds(string $answers, string $name, string $email, int $newsletter) {

    	$answers_decoded = json_decode($answers);

    	foreach ($answers_decoded AS $number => $answer) {
	    	foreach ($this->{'to_include'.$number} AS $yesno => $ids) {

	    		// Switch to randomize ID-s
	    		//$ids = $this->randomizer();

	    		$ids_exploded = explode(', ', $ids);

	    			// Buduj siłę negatywną
	    			if ($answer != $yesno) { 
	    				foreach ($ids_exploded AS $value) {
							if (!empty($this->excluded_ids[$value])) {
								$this->excluded_ids[$value]++;
							} else {
								$this->excluded_ids[$value] = 1;
							}
	    				}
	    			} 

	    			// Buduj siłę pozytywną
	    			if ($answer == $yesno) {
	    				foreach ($ids_exploded AS $value) {
	    					if (!empty($this->included_ids[$value])) {
								$this->included_ids[$value]++;
							} else {
								$this->included_ids[$value] = 1;
							}

							// Positive synergy	
	    				}
	    			} 
	    	}
    	}

    	// Synergie - wstępnie działa
    	// Na pewno kwasy / smoked / grodziskie / ciężkie RIS-y
    	// Ma podbijać sumę ID-ków w stosie (wpływ na wszystkie ID przypisane do danej odpowiedzi na tak/nie)
    	 $answer_value = get_object_vars($answers_decoded);
    	 if ($answer_value[1] == 'tak' && $answer_value[2] == 'tak') {
    	 	$this->synergy(1, 100, true);
    	 	$this->synergy(2, 100, true);
    	 }

    	// TODO: Check if there are at least 3 styles
    	// If no - make extra Draw
    	return $this->chooseStyles($name, $email, $newsletter);

    }

    public function chooseStyles(string $name, string $email, int $newsletter) {

    	// Tu musi być jeszcze wywołanie funkcji, która sprawdzi, czy dany styl nie występuje wiele razy i w included i w excluded (bo to nie miałoby sensu, gdyby stout był jednocześnie wybrany i wykluczony)
    	
    	arsort($this->included_ids);
    	arsort($this->excluded_ids);

    	// if (1==1) {
    	// 	$this->printPre($this->included_ids);
    	// 	$this->printPre($this->excluded_ids, true);
    	// }

    	// TODO: Pojedyncze selecty!
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


    public function logStyles(string $name, string $email, int $newsletter) : bool {

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
