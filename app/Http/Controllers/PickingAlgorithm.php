<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PickingAlgorithm extends Controller
{

	/**
	* Array zawiera pary 'odpowiedź' => id_piw z bazy do zaliczenia w przypadku wyboru tej odpowiedzi + ew. dodatkowa siła
	*/
	// Czy smakują Ci lekkie piwa koncernowe dostępne w sklepach?
	protected $to_include1 = array('tak' => '9:2,10:2,11:2,12,13:2,14,25,26,27,41,45,52', 
									'nie' => '5,6,7,8,22,23,24,28,29,30,31,32,33,34,35,36,37,38,39,40,42,43,44,46,47,48,49,50,51,53,54,55,56,57,58,59,60,61,62,63,64');
	// Czy chcesz poznać nowe smaki?
	protected $to_include2 = array('tak' => '1,2,3,4,5,6,7,8,15,16,19,20,22,23,24,28,29,30,31,32,33,34,35,36,37,38,39,40,42,43,44,45,47,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64', 
									'nie' => '9:2,10:2,11:2,12:1.5,13:2,14,21,25,26,27:0.5,41:2,46,48:0:5');
	// Czy wolałbyś poznać wyłącznie style, które potrafią zszokować?
	protected $to_include3 = array('tak' => '1:1.5,2:2.5,3:2.5,4:2.5,5:2.5,6:2.5,7:2.5,8:2.5,15:2.5,16:2.5,23:2.5,24:2.5,36:2.5,37:2.5,40:2.5,42:2.5,43:2.5,44:2.5,50:2.5,51:2.5,54:2.5,55:2.5,56:2.5,57:2.5,58:2.5,59:2.5,60:1.5,61:1.5,62:2.5,63:2.5', 'nie' => '');

	// TODO: Wykluczamy to, co już znasz?

	// Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego? - TODO na zakresach z beer_flavours lub API PolskiKraft
	protected $to_include4 = array('coś lekkiego' => '9,10,11,12,13,17,18,21,25,26,31,32,33,40,41,45,47,51,52', 
									'coś pośrodku' => '14,15,16,19,20,25,27,28,29,30,34,35,38,42,43,44,45,46,47,48,49,53,54,55,56,57,58,59,60,61,64', 
									'coś złożonego' => '1,2,3,4,5,6,7,8,22,23,24,36,37,39,42,43,44,50,62,63');
	
	// Jak wysoką goryczkę preferujesz?
	protected $to_include5 = array('ledwie wyczuwalną' => '9,14,15,16,17,18,19,20,25,40,41,44,50,51,53,54,56', 
									'lekką' => '11,12,21,22,23,26,31,34,42,43,45,46,47,48,49,50,52,55,57,59,60', 
									'zdecydowanie wyczuwalną' => '1,2,3,4,5,6,7,8,10,13,21,24,27,28,29,30,32,33,35,38,39,55,57,58,59,60,61,62,63,64', 
									'mocną' => '1:1.5,2:1.5,3:1.5,4:1.5,5:1.5,6:1.5,7:1.5,8:1.5,35,36,37,58,59,62,63', 
									'jestem hopheadem' => '1:1.5,3:1.5,5:1.5,7:2,8');

	// Wolisz piwa jasne czy ciemne?
	protected $to_include6 = array('jasne' => '1,2,5,6,7,8,9,10,11,13,14,15,16,17,20,22,23,25,26,27,28,31,32,38,39,40,41,42,43,44,45,46,47,49,50,51,52,53,54,55,56,57,60,61', 
									'bez znaczenia' => '', 
									'ciemne' => '3,4,12,18,19,21,24,29,30,33,34,35,36,37,43:0.5,48,58,59,62,63,64');

	// Wolisz piwa słodsze czy wytrawniejsze?
	protected $to_include7 = array('słodsze' => '1,2,5,6,7:1.5,8,14:1.5,15:1.5,16:1.5,18:1.5,19,20:1.5,22:2,23:2,25,31:1.5,34:2,36,38,39:1.5,46,49:1.5,50,53,54:2,60:1.5,62,63', 
									'bez znaczenia' => '', 
									'wytrawniejsze' => '3:1.5,4,5,9,10:1.5,11,12,13:1.5,17,18,21,28,29,33:2,35:2,36,37,40,41,45,47,48,52:1.5,55,57,58,59,61,62,63,64');

	// Czy odpowiadałby Ci smak czekoladowy w piwie?
	protected $to_include8 = array('tak' => '3:1.25,4,12:1.5, 18,21:1.25,24:2,29,30,33:1.5,34:1.75,35:1.5,36:2,37:2,48,58:1.25,59:1.5,62:2,63:2', 
									'nie' => '1,2,5,6,7,8,9,10,11,13,14,15,16,17,18,19,20,22,23,25,26,27,28,31,32,38,39,40,41,42,43,44,45,46,47,49,50,51,52,53,54,55,56,57,60,61,64');
	// Czy wolisz piwa mocno nagazowane?
	protected $to_include9 = array('tak' => '5,7,8,19,20,21,25,40,44,45,47,49,51,52,56:0.5', 
									'nie' => '1,2,3,4,6,9,10,11,12,13,14,15,16,17,18,22,23,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39,46,53,54');
	// Czy odpowiadałby Ci smak palony w piwie?
	protected $to_include10 = array('tak' => '3:2,12,21:1.25,24:1.75,29:1.5,33:1.5,35:1.25,36:1.5,37:2,58:1.25,59:1.25,62:1.5,63:1.5,64', 
									'nie' => '1,2,4,5,6,7,8,9,10,11,13,14,15,16,17,18,19,20,21,22,23,25,26,27,28,30,31,32,34,38,32,40,41,42,43,44,45,46,47,48:0.5,49,50,51,52,53,54,55,56,57,60,61');
	// Czy chciałbyś piwo w klimatach owocowych (bez soku)?
	protected $to_include11 = array('tak' => '1:2,2:1.5,5:1.5,6:1.5,7:2,8:2,25,40:1.5,42:1.5,43,44,45,47:0.5,49:1.5,50,51:1.25,55,56:1.5,60:2,61:2', 
									'nie' => '3,4,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41,46,47,48,52,53,54,57,58,59,62,63,64');
	// Co powiesz na piwo kwaśne?
	protected $to_include12 = array('tak' => '40:2,42:3,43:3,44:3,51:2,56:2', 
									'nie' => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,45,46,47,48,49,50,52,53,54,55:0.5,57,58,59,60,61,62,63,64');
	// Co powiesz na piwo słonawe?
	protected $to_include13 = array('tak' => '51:3,55:0.5', 
									'nie' => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,54,55,56,57,58,59,60,61,62,63,64');

	// Additional questions
	// BA
	protected $to_include14 = array('tak' => 'tak', 
									'nie' => 'nie');

	// Piwa o jakiej konsystencji preferujesz?
	protected $to_include15 = array('wodniste' => '33:2,40:2,44:2,51:2,52:2', 
									'średnie' => '1:2,2:2,3:2,4:2,5:2,6:2,9:2,10:2,11:2,12:2,13:2,14:2,15:2,16:2,17:2,18:2,19:2,21:2,22:2,24:2,26:2,27:2,28:2,29:2,30:2,31:2,32:2,34:2,35:2,38:2,41:2,42:2,43:2,45:2,46:2,47:2,48:2,49:2,55:2,56:2,57:2,59:2,60:2,61:2,64:2',
									'gęste' => '7:2,20:2,23:2,25:2,36:2,37:2,39:2,50:2,53:2,54:2,58:2,62:2,63:2');

	// Jak mocne alkoholowo piwa preferujesz?
	protected $to_include16 = array('lekkie' => '9:2,10:2,11:2,12:2,13:2,33:2,41:2,44:2,51:2,52:2,64:2', 
									'średnie' => '1:2,2:2,3:2,4:2,5:2,6:2,7:2,14:2,15:2,16:2,17:2,18:2,19:2,21:2,25:2,27:2,28:2,29:2,30:2,31:2,32:2,34:2,38:2,42:2,43:2,45:2,46:2,47:2,48:2,53:2,55:2,56:2,57:2,59:2,60:2,61:2',
									'mocne' => '7:2,8:2,20:2,22:2,23:2,24:2,36:2,37:2,39:2,49:2,50:2,53:2,54:2,58:2,62:2,63:2');

	// Czy odpowiadałby Ci smak wędzony/dymny w piwie?
	// TODO coś jak z BA
	protected $to_include17 = array('tak' => '15:2,16:2,58:2,59:2,62:2,62:2', 
									'nie' => '');

	private $included_ids = array(); // Beer IDs to include
	private $excluded_ids = array(); // Excluded beer IDs
	private $style_to_take = array(); // Styles user should buy
	private $additional_styles_to_take = array(); 
	private $additional_styles_to_avoid = array();
	private $style_to_avoid = array(); // Styles user should avoid

	private $must_take = false; // TODO: Obsługa wszystkich 3 stylów
	private $must_avoid = false; // TODO: Obsługa wszystkich 3 stylów

	public $BA = false; // BA beers

	private $cnt_styles_to_pick = 3;
	private $cnt_styles_to_avoid = 3;

	/**
	* Builds positive synergy if user ticks 2-4 particular answers
	*/
	private function positiveSynergy(array $ids_to_multiply, $multiplier) : void {

		foreach ($ids_to_multiply AS $id) {
			$this->included_ids[$id] *= $multiplier;
		}
		
	}

	/**
	* Builds negative synergy if user ticks 2-4 particular answers
	*/
	private function negativeSynergy(array $ids_to_divide, $divider) : void {

		foreach ($ids_to_divide AS $id) {
			$this->excluded_ids[$id] = floor($this->excluded_ids[$id] / $divider);
		}
		
	}

	/**
	* Positive and negative synergies executer
	* There are all the synergies
	*/
	private function synergyExecuter($answer_value) : void {

		// Lekkie + owocowe + Kwaśne
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[11] == 'tak' && $answer_value[12] == 'chętnie') {
    	 	$this->positiveSynergy(array(40, 56), 2);
    	 }
    	 // nowe smaki LUB szokujące + złożone + jasne
    	 if (($answer_value[2] == 'tak' || $answer_value[3] == 'tak') && $answer_value[4] == 'coś złożonego' && $answer_value[4] == 'jasne') {
    	 	$this->positiveSynergy(array(7, 15, 16, 23, 39, 42, 50, 60), 2);
    	 }

    	 // nowe smaki LUB szokujące + złożone + ciemne (beczki!)
    	 if (($answer_value[2] == 'tak' || $answer_value[3] == 'tak') && $answer_value[4] == 'coś złożonego' && $answer_value[4] == 'ciemne') {
    	 	$this->positiveSynergy(array(36, 37, 58, 59, 62, 63), 2);
    	 }

    	 // złożone + ciemne + nieowocowe
    	 if ($answer_value[4] == 'coś złożonego' && $answer_value[6] == 'ciemne' && $answer_value[11] == 'nie') {
    	 	$this->positiveSynergy(array(3, 24, 35, 36, 37, 48, 58, 59, 62, 63), 1.5);
    	 }

    	 // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna)
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[6] == 'ciemne' && $answer_value[7] == 'słodsze' && !in_array($answer_value[5], array('mocną', 'jestem hopheadem'))) {
    	 	$this->positiveSynergy(array(12, 29, 30, 34, 64), 2);
    	 	$this->negativeSynergy(array(36, 37), 3);
    	 }

    	 // jasne + nieczekoladowe + niepalone
    	 if ($answer_value[6] == 'jasne' && $answer_value[8] == 'nie' && $answer_value[10] == 'nie') {
    	 	$this->negativeSynergy(array(12, 21, 24, 29, 33, 34, 35, 36, 37, 58, 59, 62, 63), 2);
    	 }

    	 // czekoladowe + niepalone
    	 if ($answer_value[8] == 'tak' && $answer_value[10] == 'nie') {
    	 	$this->positiveSynergy(array(12, 34), 1.5);
    	 }

    	 // ciemne + czekoladowe + niepalone
    	 if ($answer_value[8] == 'tak' && $answer_value[10] == 'nie') {
    	 	$this->positiveSynergy(array(12, 34), 2);
    	 }

    	 // goryczka ledwo || lekka + jasne + mocno gazowane
    	 if (($answer_value[5] == 'ledwie wyczuwalną' || $answer_value[5] == 'lekką') && $answer_value[6] == 'jasne' && $answer_value[9] == 'tak') {
    	 	$this->positiveSynergy(array(20, 25, 40, 44, 45, 47, 51, 52), 2);
    	 }

    	 // jasne + lekkie + wędzone = grodziskie
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[6] == 'jasne' && $answer_value[17] == 'tak') {
    	 	$this->positiveSynergy(array(52), 2.5);
    	 }

    	 // jasne + lekkie + wodniste + wędzone = grodziskie
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[6] == 'jasne' && $answer_value[15] == 'wodniste' && $answer_value[17] == 'tak') {
    	 	$this->positiveSynergy(array(52), 3);
    	 	// $this->negativeSynergy <- RIS-y i gęste lochy TODO
    	 }

    	 // duża/hophead goryczka + jasne
    	 if (($answer_value[5] == 'mocną' || $answer_value[5] == 'jestem hopheadem') && $answer_value[6] == 'jasne') {
    	 	$this->positiveSynergy(array(1, 2, 5, 6, 7, 8, 28, 61), 1.5);
    	 }

		 // duża/hophead goryczka + ciemne (Black/Brown IPA + Porter)
    	 if (($answer_value[5] == 'mocną' || $answer_value[5] == 'jestem hopheadem') && $answer_value[6] == 'ciemne') {
    	 	$this->positiveSynergy(array(3, 4, 29), 1.5);
    	 }

    	 // niska goryczka + gęste + owoce
    	 if (($answer_value[5] == 'ledwie wyczuwalną' || $answer_value[5] == 'lekką') && $answer_value[7] != 'wytrawniejsze' && $answer_value[11] == 'tak') {
    	 	$this->positiveSynergy(array(20, 25, 45, 53), 2);
    	 	// negative
    	 }

	}

	/*
	* Buduje siłę dla konkretnych ID stylu
	* Jeśli id ma postać 5:2.5 to zwiększy (przy trafieniu w to ID) punktację tego stylu o 2.5 a nie o 1
	* Domyślnie zwiększa punktację stylu o 1
	*/
	private function strengthBuilder(string $ids, bool $shocking = false) : ?array {

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
	* Excludes sour/salty/smoked beers if user says NO
	* TODO: Funkcja, która działa z każdym pytaniem
	*/
	private function excluder(array $ids_to_exclude) : void {
		foreach ($ids_to_exclude AS $id) {
			$this->included_ids[$id] = 0;
		}
	}

	/**
	* Prevents beers to be both included and excluded
	*/
	private function checkDoubles() : bool {

		$included = array_slice($this->included_ids, 0, $this->cnt_styles_to_pick, true);
		$excluded = array_slice($this->excluded_ids, 0, $this->cnt_styles_to_avoid, true);

		foreach ($included AS $id => $points) {
			if (array_key_exists($id, $excluded)) {
				$to_unset = $id;
			}
		}

		if (is_numeric($to_unset)) {
			unset($this->included_ids[$to_unset]);
			return true;
		} else {
			return false;
		}

	}

	/**
	* If there's an 4th and 5rd style with a little "margin" to 3rd style
	* Takes 4th or 5th style as an extra styles to take or avoid
	* TODO: Do jednej zmiennej pakować i w widoku 4-5 styl pokazywać inaczej
	*/
	private function optionalStyles() : void {

		$third_style_take = key(array_slice($this->included_ids, 0, 3, true));
		$third_style_avoid = key(array_slice($this->excluded_ids, 0, 3, true));

		for ($i = 3; $i <= 4; $i++) {

			$to_take_chunk = array_slice($this->included_ids, $i, 1, true);
			$to_avoid_chunk = array_slice($this->excluded_ids, $i, 1, true);
			
			if (($third_style_take / 100 * 90) >= $to_take_chunk) {
				$this->cnt_styles_to_pick++;
			}

			if (($third_style_avoid / 100 * 90) >= $to_avoid_chunk) {
				$this->cnt_styles_to_avoid++;
			}
		}

	}

	/**
	* If 1st styles to take and avoid has more than/equal 150% points of 2nd styles
	* Emphasize them!
	*/
	private function mustTakeMustAvoid() : void {

		$first_style_take = array_values(array_slice($this->included_ids, 0, 1, true));
		$first_style_avoid = array_values(array_slice($this->excluded_ids, 0, 1, true));

		$second_style_take = array_values(array_slice($this->included_ids, 1, 1, true));
		$second_style_avoid = array_values(array_slice($this->excluded_ids, 1, 1, true));

		if ($second_style_take[0] * 1.5 <= $first_style_take[0]) {
			$this->must_take = true;
		}

		if ($second_style_avoid[0] * 1.5 <= $first_style_avoid[0]) {
			$this->must_avoid = true;
		}

	}
    
    /**
    * Heart of an algorithm
    */
    public function includeBeerIds(string $answers, string $name, string $email, int $newsletter) {

    	$answers_decoded = json_decode($answers);

    	foreach ($answers_decoded AS $number => $answer) {
	    	foreach ($this->{'to_include'.$number} AS $yesno => $ids) {

	    		if ($_POST['answer-14'] == 'tak') {
	    			$this->BA = true;
	    		} else {
	    			$this->BA = false;
	    		}

	    		//TODO: Refactor
	    		if ($_POST['answer-12'] == 'nie ma mowy') {
	    			$to_excl = array(40, 42, 43, 44, 51, 56);
	    			$this->excluder($to_excl);
	    		}
				if ($_POST['answer-13'] == 'nie ma mowy') {
	    			$to_excl = array(51);
	    			$this->excluder($to_excl);
	    		}
	    		if ($_POST['answer-17'] == 'nie') {
	    			$to_excl = array(15, 16, 57, 58, 59, 62, 63);
	    			$this->excluder($to_excl);
	    		}

	    		// Nie idź dalej przy BA
	    		if (in_array($ids, array('tak', 'nie'))) {
	    			continue;
	    		}

	    		//if ($_POST['answer-3'] == 'nie') {
	    		// TODO!
	    			//$ids_to_calc = $this->strengthBuilder($ids, true);
	    		//} else {
	    			$ids_to_calc = $this->strengthBuilder($ids);
	    		//}
	    		
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

    	// Na pewno kwasy / smoked / grodziskie / ciężkie RIS-y / AIPA
    	// Ma podbijać sumę ID-ków w stosie (wpływ na wszystkie ID przypisane do danej odpowiedzi na tak/nie)
    	// TODO: Refactor na tablice
    	 $answer_value = get_object_vars($answers_decoded);
    	 $this->synergyExecuter($answer_value);

    	// TODO: Jeśli style zapunktowały tak samo, to który ma brać?
    	return $this->chooseStyles($name, $email, $newsletter);

    }

    public function chooseStyles(string $name, string $email, int $newsletter) {
    		
    	arsort($this->included_ids);
    	arsort($this->excluded_ids);
    	$this->optionalStyles();
    	$this->checkDoubles();
    	$this->mustTakeMustAvoid();

    	if ($_SERVER['REMOTE_ADDR'] == '89.64.48.198') {
	    	echo "Tablica ze stylami do wybrania i punktami: <br />";
	    	$this->printPre($this->included_ids);
	    	echo "<br />Tablica ze stylami do odrzucenia i punktami: <br />";
	    	$this->printPre($this->excluded_ids);
    	}

    	for ($i = 0; $i < $this->cnt_styles_to_pick; $i++) {
    		$style_to_take = $this->style_to_take[] = key(array_slice($this->included_ids, $i, 1, true));
    		$buythis[] = DB::select("SELECT * FROM beers WHERE id = $style_to_take");
    	}   	

    	for ($i = 0; $i < $this->cnt_styles_to_avoid; $i++) {
    		$style_to_avoid = $this->style_to_avoid[] = key(array_slice($this->excluded_ids, $i, 1, true));
    		$avoidthis[] = DB::select("SELECT * FROM beers WHERE id = $style_to_avoid");	
    	}

    	// Zapisz wybór do bazy
    	try {
    		$this->logStyles($name, $email, $newsletter);
    	} catch (Exception $e) {
    		//mail('kontakt@piwolucja.pl', 'logStyles Exception', $e->getMessage());
    	}

    	// TODO: Usuwa BA jeśli nie trafiono w 3 polecanych na piwa, które leżakuje się na ogół w beczkach - przemyśleć, czy warto!
    	// foreach ($this->style_to_take AS $ids) {
    	// 	if (!in_array($ids, array(7, ))) {
    	// 		$this->BA = false;
    	// 	}
    	// }

    	return view('results', ['buythis' => $buythis, 'avoidthis' => $avoidthis, 'must_take' => $this->must_take, 'must_avoid' => $this->must_avoid, 'username' => $name, 'barrel_aged' => $this->BA]);

    }

    private function logStyles(string $name, string $email, int $newsletter) : void {

    	$lastID = DB::select('SELECT MAX(id_answer) AS lastid FROM `styles_logs` LIMIT 1');
    	$nextID = (int)$lastID[0]->lastid + 1;

    	for ($i = 0; $i < 3; $i++) {
    		DB::insert('INSERT INTO `styles_logs` (id_answer, username, email, newsletter, style_take, style_avoid, ip_address, created_at)
    					VALUES
    					(?, ?, ?, ?, ?, ?, ?, ?)', [(int)$nextID, $name, $email, $newsletter, $this->style_to_take[$i], $this->style_to_avoid[$i], $_SERVER['REMOTE_ADDR'], NOW()]);
    	}

    }


}
