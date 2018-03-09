<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PickingAlgorithm extends Controller
{

	/*
	* Array zawiera pary 'odpowiedź' => id_piw z bazy do zaliczenia w przypadku wyboru tej odpowiedzi + ew. dodatkowa siła
	*/
	// Czy smakują Ci lekkie piwa koncernowe dostępne w sklepach?
	protected $to_include1 = array('tak' => '9, 10, 11, 12, 13, 14, 25, 26, 27, 41,45,52', 
									'nie' => '5, 6, 7, 8, 22, 23, 24, 28, 29, 30, 31, 32, 33, 34, 35, 36,37,38,39,40,42,43,44,46,47,48,49,50,51,53,54,55,56,57,58,59,60'); // Nie można uzupełniać jako odwrotność tak, ale trzeba z tym uważać (czasem może być ani nie, ani tak)
	// Czy chcesz poznać nowe smaki?
	protected $to_include2 = array('tak' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 15, 16, 19, 20, 22,23,24,28,29,30,31,32,33,34,35,36,37,38,39,40,42,43,44,45,47,49,50,51,52,53,54,55,56,57,58,59,60', 
									'nie' => '1, 3, 5, 7, 21, 25,26,27,41,46,48');
	// Czy wolałbyś poznać wyłącznie style, które potrafią zszokować?
	protected $to_include3 = array('tak' => '1:1.5,2:2.5,3:2.5,4:2.5,5:2.5,6:2.5,7:2.5,8:2.5,15:2.5,16:2.5,23:2.5,24:2.5,36:2.5,37:2.5,40:2.5,42:2.5,43:2.5,44:2.5,50:2.5,51:2.5,54:2.5,55:2.5,56:2.5,57:2.5,58:2.5,59:2.5,60:2.5', 'nie' => '');

	// TODO: Wykluczamy to, co już znasz?

	// Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego? - TODO na zakresach z beer_flavours lub API PolskiKraft
	protected $to_include4 = array('coś lekkiego' => '9,10,11,12,13,17,18,21,25,26,31,32,33,40,41,45,47,51,52', 
									'coś pośrodku' => '14,15,16,19,20,25,27,28,29,30,34,35,38,42,43,44,45,46,47,48,49,53,54,55,56,57,58,59,60', 
									'coś złożonego' => '1,2,3,4,5,6,7,8,22,23,24,36,37,39,42,43,44,50,60:0.5');
	
	// Jak wysoką goryczkę preferujesz?
	protected $to_include5 = array('ledwie wyczuwalną' => '9, 14, 15, 16, 17, 18, 19, 20, 25, 40,41,44,50,51,53,54,56', 
									'lekką' => '11, 12, 21, 22, 23, 26, 31,34,42,43,45,46,47,48,49,50,52,55,57,59', 
									'zdecydowanie wyczuwalną' => '10, 13, 21, 24, 27, 28,29,30,32,33,35,38,39,55,57,58,59,60', 
									'mocną' => '1, 2, 3, 4, 5, 6, 7, 8,36,37,58,59', 
									'jestem hopheadem' => '1:1.5, 3:1.5, 7:2');

	// Wolisz piwa jasne czy ciemne? (zmienić na jasne/ciemne)
	protected $to_include6 = array('jasne' => '1, 2, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 20,22,23,25,26,27,28,31,32,38,39,40,41,42,43,44,45,46,47,49,50,51,52,53,54,55,56,57,60', 
									'bez znaczenia' => '', 
									'ciemne' => '3, 4, 12, 18, 19,21,24,29,30,33,34,35,36,37,43:0.5,48,58,59');

	// Wolisz piwa słodsze czy wytrawniejsze?
	protected $to_include7 = array('słodsze' => '1, 2,5,6,7,8,14,15,16,18,19,20,21,46,49,50,53,54,60', 
									'bez znaczenia' => '', 
									'wytrawniejsze' => '3,4,5,9,10,11,12,13,17,21,41,45,47,48,52,55,57,58,59');
	// TODO: Jako skala/suwak
	// Czy odpowiadałby Ci smak czekoladowy w piwie?
	protected $to_include8 = array('tak' => '3, 4, 12, 18,21,24,29,30,33,34,35,36,37,48,58:1.25,59:1.25', 
									'nie' => '1, 2, 5, 6, 7, 8, 9, 19, 11, 13, 14, 15, 16, 17, 18, 19, 20,22,23,25,26,27,28,31,32,38,39,40,41,42,43,44,45,46,47,49,50,51,52,53,54,55,56,57,60');
	// Czy wolisz piwa mocno nagazowane?
	protected $to_include9 = array('tak' => '5, 7, 8, 19, 20,21,25,40,44,45,47,49,51,52,56:0.5', 
									'nie' => '1, 2, 3, 4, 6, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 22,23,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39,46,53,54');
	// Czy odpowiadałby Ci smak palony w piwie?
	protected $to_include10 = array('tak' => '3, 12,21,24,29,33,35,36,37,58:1.25,59:1.25', 
									'nie' => '1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20,21,22,23,25,26,27,28,30,31,32,34,38,32,40,41,42,43,44,45,46,47,48:0.5,49,50,51,52,53,54,55,56,57,60');
	// Czy chciałbyś piwo w klimatach owocowych (bez soku)?
	protected $to_include11 = array('tak' => '1:2, 2:2, 5:2, 6:2, 7:2, 8:2,25,40,42,43,44,45:0.5,47:0.5,51,56,60:2', 
									'nie' => '3, 4, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,21,22,23,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39');
	// Co powiesz na piwo kwaśne?
	protected $to_include12 = array('tak' => '40:2,42:3,43:3,44:3,51:2,56:2', 
									'nie' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,45,46,47,48,49,50,52,53,54,55:0.5,57,58,59,60');
	// Co powiesz na piwo słonawe?
	protected $to_include13 = array('tak' => '51:3, 55:0.5', 
									'nie' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,54,55,56,57,58,59,60');

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

	private function positiveSynergy(array $ids_to_multiply, int $multiplier) {

		foreach ($ids_to_multiply AS $id) {
			$this->included_ids[$id] *= $multiplier;
		}
		
	}

	private function negativeSynergy(array $ids_to_divide, int $divider) {

		foreach ($ids_to_divide AS $id) {
			$this->excluded_ids[$id] = floor($this->excluded_ids[$id] / $divider);
		}
		
	}

	/*
	* Buduje siłę dla konkretnych ID stylu
	* Jeśli id ma postać 5:2.5 to zwiększy (przy trafieniu w to ID) punktację tego stylu o 2.5 a nie o 1
	* Domyślnie większa punktację stylu o 1
	*/
	private function strengthBuilder(string $ids) : ?array {

		$ids_exploded = explode(',', trim($ids));
		foreach ($ids_exploded AS $v) {
	    	if (stristr($v, ':') || stristr($v, ' :')) {
	    		$tmp = explode(':', $v);
	    		$strength[$tmp[0]] = (float)$tmp[1];
	    	} else {
	    		$strength[$v] = 1;
	    	}
	    }

	    return $strength;

	}

    
    /**
    * Tutaj dzieje się magia
    */
    public function includeBeerIds(string $answers, string $name, string $email, int $newsletter) {

    	$answers_decoded = json_decode($answers);

    	foreach ($answers_decoded AS $number => $answer) {
	    	foreach ($this->{'to_include'.$number} AS $yesno => $ids) {

	    		//$ids = $this->randomizer(); Switch to randomize ID-s

	    		$ids_to_calc = $this->strengthBuilder($ids);
	    		
	    		if ($answer == $yesno) {
		    		foreach ($ids_to_calc AS $style_id => $strength) {
		    			$this->included_ids[$style_id] += $strength;
		    		}
	    		}

	    		if ($answer != $yesno) { 
		    		foreach ($ids_to_calc AS $style_id => $strength) {
		    			$this->excluded_ids[$style_id] += $strength;
		    		}
	    		} 

	    	}
    	}

    	// Synergie - wstępnie działa
    	// Na pewno kwasy / smoked / grodziskie / ciężkie RIS-y
    	// Ma podbijać sumę ID-ków w stosie (wpływ na wszystkie ID przypisane do danej odpowiedzi na tak/nie)
    	 $answer_value = get_object_vars($answers_decoded);
    	 // Lekkie + owocowe + Kwaśne
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[11] == 'tak' && $answer_value[12] == 'tak') {
    	 	$this->positiveSynergy(array(40), 3);
    	 }
    	 // nowe smaki LUB szokujące + złożone + jasne
    	 if (($answer_value[2] == 'tak' || $answer_value[3] == 'tak') && $answer_value[4] == 'coś złożonego' && $answer_value[4] == 'jasne') {
    	 	$this->positiveSynergy(array(7, 15, 16, 23, 39), 3);
    	 }

    	 // nowe smaki LUB szokujące + złożone + ciemne (beczki!)
    	 if (($answer_value[2] == 'tak' || $answer_value[3] == 'tak') && $answer_value[4] == 'coś złożonego' && $answer_value[4] == 'ciemne') {
    	 	$this->positiveSynergy(array(36, 37), 3);
    	 }

    	 // złożone + ciemne + nieowocowe
    	 if ($answer_value[4] == 'coś złożonego' && $answer_value[6] == 'ciemne' && $answer_value[11] == 'nie') {
    	 	$this->positiveSynergy(array(3, 24, 35, 36, 37), 3);
    	 }

    	// TODO: Check if there are at least 3 styles
    	// If no - make extra Draw
    	return $this->chooseStyles($name, $email, $newsletter);

    }

    public function chooseStyles(string $name, string $email, int $newsletter) {

    	// Tu musi być jeszcze wywołanie funkcji, która sprawdzi, czy dany styl nie występuje wiele razy i w included i w excluded (bo to nie miałoby sensu, gdyby stout był jednocześnie wybrany i wykluczony)
    	
    	arsort($this->included_ids);
    	arsort($this->excluded_ids);

    	echo "Tablica ze stylami do wybrania i punktami: <br />";
    	var_dump($this->included_ids);
    	echo "<br />Tablica ze stylami do odrzucenia i punktami: <br />";
    	var_dump($this->excluded_ids);


    	for ($i = 0; $i < self::STYLES_TO_PICK; $i++) {
    		$style_to_take = $this->style_to_take[] = key(array_slice($this->included_ids, $i, 1, true));
    		$buythis[] = DB::select("SELECT * FROM beers WHERE id = $style_to_take");
    	}   	

    	for ($i = 0; $i < self::STYLES_TO_PICK; $i++) {
    		$style_to_avoid = $this->style_to_avoid[] = key(array_slice($this->excluded_ids, $i, 1, true));
    		$avoidthis[] = DB::select("SELECT * FROM beers WHERE id = $style_to_avoid");	
    	}

    	// Zapisz wybór do bazy
    	try {
    		$this->logStyles($name, $email, $newsletter);
    	} catch (Exception $e) {
    		//mail('kontakt@piwolucja.pl', 'logStyles Exception', $e->getMessage());
    	}

    	return view('results', ['buythis' => (array)$buythis, 'avoidthis' => (array)$avoidthis, 'username' => $name]);

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
