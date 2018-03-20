<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\PolskiKraft\PolskiKraftAPI AS PKAPI;

class PickingAlgorithm extends Controller
{

	/**
	* Array zawiera pary 'odpowiedź' => id_piw z bazy do zaliczenia w przypadku wyboru tej odpowiedzi + ew. dodatkowa siła
	*/
	// Czy smakują Ci lekkie piwa koncernowe dostępne w sklepach?
	protected $to_include1 = array('tak' => '9:2,10:2,11:2,12:2,13:2,14:2,25:2,27:2,45:2,52:2,68:2,70:2,72:2,76:2', 
									'nie' => '5,6,7,8,22,23,24,28,30,32,33,34,35,36,37,38,39,40,42,44,47,48,49,50,51,53,55,56,57,58,59,60,61,62,63,64,65,67,69,71,73,74,75');
	// Czy chcesz poznać nowe smaki?
	protected $to_include2 = array('tak' => '1,2,3,4,5,6,7,8,15,16,19,20,22,23,24,28,30,32,33,34,35,36,37,38,39,40,42,44,45,47,49,50,51,52,53,55,56,57,58,59,60,61,62,63,64,65,67,69,70,71,72,73,74,75', 
									'nie' => '9:2,10:2,11:2,12:1.5,13:2,14,21,25,27:0.5,48:0:5,68,72:2');
	// Czy wolałbyś poznać wyłącznie style, które potrafią zszokować?
	protected $to_include3 = array('tak' => '1:1.5,2:2.5,3:2.5,5:2.5,6:2.5,7:2.5,8:2.5,15:2.5,16:2.5,23:2.5,24:2.5,36:2.5,37:2.5,40:2.5,42:2.5,44:2.5,50:2.5,51:2.5,55:2.5,56:2.5,57:2.5,58:2.5,59:2.5,60:1.5,61:1.5,62:2.5,63:2.5,73:2.5,74:2.5,75:2.5', 'nie' => '');

	// Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego?
	protected $to_include4 = array('coś lekkiego' => '9,10,11,12,13,21,25,32,33,40,45,47,51,52', 
									'coś pośrodku' => '14,15,16,19,20,25,27,28,30,34,35,38,42,44,45,47,48,49,53,55,56,57,58,59,60,61,64', 
									'coś złożonego' => '1,2,3,5,6,7,8,22,23,24,36,37,39,42,44,50,62,63');
	
	// Jak wysoką goryczkę preferujesz?
	protected $to_include5 = array('ledwie wyczuwalną' => '9,14,15,16,19,20,25,40,44,45,50,51,53,56', 
									'lekką' => '11,12,21,22,23,34,42,45,47,48,49,50,52,55,57,59,60', 
									'zdecydowanie wyczuwalną' => '1,2,3,5,6,7,8,10,13,21,24,27,28,30,32,33,35,38,39,55,57,58,59,60,61,62,63,64', 
									'mocną' => '1,2,3,5,6,7,8,35,36,37,58,59,62,63', 
									'jestem hopheadem' => '1,3,5,7,8');

	// Wolisz piwa jasne czy ciemne?
	protected $to_include6 = array('jasne' => '1:2.5,2:2.5,5:2.5,6:2.5,7:2.5,8:2.5,9:2.5,10:2.5,11:2.5,13:2.5,14:2.5,15:2.5,16:2.5,20:2.5,22:2.5,23:2.5,25:2.5,27:2.5,28:2.5,32:2.5,38:2.5,39:2.5,40:2.5,42:2.5,44:2.5,45:2.5,47:2.5,49:2.5,50:2.5,51:2.5,52:2.5,53:2.5,55:2.5,56:2.5,57:2.5,60:2.5,61:2.5,65:2.5,67:2.5,68:2.5,69:2.5,70:2.5,72:2.5,73:2.5', 
									'bez znaczenia' => '', 
									'ciemne' => '3:2.5,12:2.5,19:2.5,21:2.5,24:2.5,30:2.5,33:2.5,34:2.5,35:2.5,36:2.5,37:2.5,48:2.5,58:2.5,59:2.5,62:2.5,63:2.5,64:2.5,71:2.6,47:2.5,75:2.5,76:2.5');

	// Wolisz piwa słodsze czy wytrawniejsze?
	protected $to_include7 = array('słodsze' => '1,2,5,6,7:1.5,8,14:1.5,15:1.5,16:1.5,19,20:1.5,22:2,23:2,25,34:2,36,38,39:1.5,49:1.5,50,53,60:1.5,62,63,67:2.5,68,69,73:2,75,76', 
									'bez znaczenia' => '', 
									'wytrawniejsze' => '3:1.5,5,9,10:1.5,11,12,13:1.5,21,28,33:2,35:2,36,37,40,45,47,48,52:1.5,55,57,58,59,61,62,63,64,65,70,71,72,74,75');
		// Jak mocne i gęste piwa preferujesz?
	protected $to_include8 = array('wodniste i lekkie' => '9:4,10:4,11:4,12:4,13:4,33:4,44:4,51:4,52:4,64:4,45:4,64:4,68:4,70:4,72:4', 
									'średnie' => '1:4,2:4,3:4,5:4,6:4,7:4,14:4,15:4,16:4,19:4,21:4,25:4,27:4,28:4,29:4,30:4,32:4,34:4,38:4,42:4,47:4,48:4,53:4,55:4,56:4,57:4,59:4,60:4,61:4,64:4,69:4,71:4,72:4,73:4,74:4,75:4,75:5',
									'mocne i gęste' => '7:4,8:4,20:4,22:4,23:4,24:4,36:4,37:4,39:4,49:4,50:4,53:4,58:4,62:4,63:4,67:4,74:2,75:2');
	//
	// Czy odpowiadałby Ci smak czekoladowy w piwie?
	protected $to_include9 = array('tak' => '3:1.5,12:1.5,21:1.5,24:2,30,33:1.5,34:2,35:2,36:2,37:2,48,58:2,59:1.5,62:2,63:2,71:1.5,74,75:1.5', 
									'nie' => '1,2,5,6,7,8,9,10,11,13,14,15,16,19,20,22,23,25,27,28,32,38,39,40,42,44,45,47,49,50,51,52,53,55,56,57,60,61,64,65,67,68,69,70,72,73,76');
	// Czy odpowiadałby Ci smak kawowy w piwie?
	protected $to_include10 = array('tak' => '3,24,30,33,34,35,36,37,58,59,62,63,71,74:3,75', 
									'nie' => '1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,25,27,28,30,32,38,39,40,42,44,45,46,47,48,49,50,51,52,53,55,56,57,60,61,64,65,67,68,69,70,72,73,76');
	// Czy odpowiadałoby Ci piwo nieco przyprawowe
	protected $to_include11 = array('tak' => '2:1.5,20,25,45:1.5,47:1.5,48,49:2,50:2,53:1.5,67:1.5,68', 
									'nie' => '');
	// Czy chciałbyś piwo w klimatach owocowych (bez soku)?
	protected $to_include12 = array('tak' => '1:2,2:1.5,5:1.5,6:1.5,7:2,8:2,25,40:1.5,42:1.5,44,45,47:0.5,49:1.5,50,51:1.25,55,56:1.5,60:2,61:2,65:1.5,67,69:2,70:1.5,72,73:2.5,76', 
									'nie' => '3,9,10,11,12,13,14,15,16,19,20,21,22,23,24,27,28,30,32,33,34,35,36,37,38,39,47,48,52,53,57,58,59,62,63,64,68,71,74,75');
	// Co powiesz na piwo kwaśne?
	protected $to_include13 = array('tak' => '40:2,42:3,44:3,51:2,56:2', 
									'nie' => '1,2,3,5,6,7,8,9,10,11,12,13,14,15,16,19,20,21,22,23,24,25,27,28,30,32,33,34,35,36,37,38,39,40,45,47,48,49,50,52,53,55:0.5,57,58,59,60,61,62,63,64,65,67,68,69,70,71,72,73,74,75,76');
	// Czy odpowiadałby Ci smak wędzony/dymny w piwie?
	protected $to_include14 = array('tak' => '15:1.5,16:1.5,52:1.5,58:1.5,59:1.5,62:1.5,63:1.5', 
									'nie' => '');

	// BA
	protected $to_include15 = array('tak' => 'tak', 
									'nie' => 'nie');


	public $answers_decoded = array();

	private $included_ids = array(); // Beer IDs to include
	private $excluded_ids = array(); // Excluded beer IDs
	private $style_to_take = array(); // Styles user should buy
	private $additional_styles_to_take = array(); 
	private $additional_styles_to_avoid = array();
	private $style_to_avoid = array(); // Styles user should avoid

	private $must_take = false; // TODO: Obsługa wszystkich 3 stylów
	private $must_avoid = false; // TODO: Obsługa wszystkich 3 stylów

	public $BA = false; // BA beers
	private $shuffled = false;

	private $cnt_styles_to_pick = 3;
	private $cnt_styles_to_avoid = 3;

	// private $toshuffle = 0;

	/**
	* Builds positive synergy if user ticks 2-4 particular answers
	* TODO: Synergy Class
	*/
	private function positiveSynergy(array $ids_to_multiply, $multiplier) : void {

		foreach ($ids_to_multiply AS $id) {
			$this->included_ids[$id] *= $multiplier;
		}
		
	}

	/**
	* Builds negative synergy if user ticks 2-4 particular answers
	* TODO: Synergy Class
	*/
	private function negativeSynergy(array $ids_to_divide, $divider) : void {

		foreach ($ids_to_divide AS $id) {
			$this->excluded_ids[$id] = floor($this->excluded_ids[$id] / $divider);
		}
		
	}

	/**
	* Positive and negative synergies executer
	* There are all the synergies
	* TODO: Synergy Class
	*/
	private function synergyExecuter($answer_value) : void {

		// Lekkie + owocowe + Kwaśne
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[12] == 'tak' && $answer_value[13] == 'chętnie') { 
    	 	echo "Synergia Lekkie + owocowe + Kwaśne <br />";
    	 	$this->positiveSynergy(array(40, 56), 2);
    	 	$this->positiveSynergy(array(51), 1.5);
    	 }
    	 // nowe smaki LUB szokujące + złożone + jasne
    	 if (($answer_value[2] == 'tak' || $answer_value[3] == 'tak') && $answer_value[4] == 'coś złożonego' && $answer_value[4] == 'jasne') {
    	 	echo "Synergia we smaki LUB szokujące + złożone + jasne <br />";
    	 	$this->positiveSynergy(array(7, 15, 16, 23, 39, 42, 50, 60, 73), 2);
    	 }

    	 // nowe smaki LUB szokujące + złożone + ciemne
    	 if (($answer_value[2] == 'tak' || $answer_value[3] == 'tak') && $answer_value[4] == 'coś złożonego' && $answer_value[4] == 'ciemne') {
    	 	echo "Synergia nowe smaki LUB szokujące + złożone + ciemne <br />";
    	 	$this->positiveSynergy(array(36, 37, 58, 59, 62, 63), 2);
    	 }

    	 // złożone + ciemne + nieowocowe
    	 if ($answer_value[4] == 'coś złożonego' && $answer_value[6] == 'ciemne' && $answer_value[12] == 'nie') {
    	 	echo "Synergia złożone + ciemne + nieowocowe <br />";
    	 	$this->positiveSynergy(array(3, 24, 35, 36, 37, 48, 58, 59, 62, 63, 75), 1.5);
    	 }

    	 // złożone + ciemne + nieowocowe + kawowe
    	 if ($answer_value[4] == 'coś złożonego' && $answer_value[6] == 'ciemne' && $answer_value[10] == 'tak' && $answer_value[12] == 'nie') {
    	 	echo "Synergia złożone + ciemne + nieowocowe + kawowe <br />";
    	 	$this->positiveSynergy(array(74), 2.5);
    	 }

    	 // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna)
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[6] == 'ciemne' && $answer_value[7] == 'słodsze' && !in_array($answer_value[5], array('mocną', 'jestem hopheadem'))) {
    	 	echo "Synergia Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna) <br />";
    	 	$this->positiveSynergy(array(12, 29, 30, 34, 64), 2);
    	 	$this->negativeSynergy(array(36, 37), 3);
    	 }

    	 // jasne + nieczekoladowe
    	 if ($answer_value[6] == 'jasne' && $answer_value[9] == 'nie') {
    	 	echo "Synergia jasne + nieczekoladowe <br />";
    	 	$this->negativeSynergy(array(12, 21, 24, 29, 33, 34, 35, 36, 37, 58, 59, 62, 63, 71, 74, 75), 2);
    	 }

    	 // ciemne + czekoladowe + lżejsze
    	 if ($answer[6] == 'ciemne' && $answer_value[9] == 'tak' && $answer_value[8] != 'mocne i gęste') { 
    	 	echo "Synergia ciemne + czekoladowe + lżejsze <br />";
    	 	$this->positiveSynergy(array(12, 31, 33, 34, 35, 59, 71), 2.5);
    	 }


    	 // goryczka ledwo || lekka + jasne + nieczkoladowe + niegęste
    	 if (($answer_value[5] == 'ledwie wyczuwalną' || $answer_value[5] == 'lekką') && $answer_value[6] == 'jasne' && $answer_value[9] == 'nie' && $answer_value[8] != 'mocne i gęste') {
    	 	echo "Synergia goryczka ledwo || lekka + jasne + nieczkoladowe + niegęste <br />";
    	 	$this->positiveSynergy(array(20, 25, 40, 44, 45, 47, 51, 52, 53, 68, 73), 2);
    	 	$this->negativeSynergy(array(3, 24, 35, 36, 37, 58, 59, 62, 63, 71, 75), 2);
    	 }

    	 // jasne + lekkie + wodniste + wędzone = grodziskie
    	 if ($answer_value[4] == 'coś lekkiego' && $answer_value[6] == 'jasne' && $answer_value[8] == 'wodniste' && $answer_value[14] == 'tak') { 
    	 	echo "Synergia asne + lekkie + wodniste + wędzone = grodziskie <br />";
    	 	$this->positiveSynergy(array(52), 3);
    	 	$this->negativeSynergy(array(3, 22, 23, 24, 35, 36, 37, 50, 58, 59, 62, 63, 71, 75), 2);
    	 }

    	 // duża/hophead goryczka + jasne
    	 if (($answer_value[5] == 'mocną' || $answer_value[5] == 'jestem hopheadem') && $answer_value[6] == 'jasne') {
    	 	echo "Synergia duża/hophead goryczka + jasne <br />";
    	 	$this->positiveSynergy(array(1, 2, 5, 6, 7, 8, 28, 61), 1.75);
    	 	$this->positiveSynergy(array(65, 69, 70, 72), 1.5);
    	 }

		 // duża/hophead goryczka + ciemne
    	 if (($answer_value[5] == 'mocną' || $answer_value[5] == 'jestem hopheadem') && $answer_value[6] == 'ciemne') {
    	 	echo "Synergia duża/hophead goryczka + ciemne <br />";
    	 	$this->positiveSynergy(array(3, 36, 37, 58, 62, 63, 75), 1.75);
    	 }

    	 // goryczka ledwo || lekka
    	 if ($answer_value[5] == 'ledwie wyczuwalną' || $answer_value[5] == 'lekką') {
    	 	echo "Synergia negatywna na lekkie goryczki <br />";
    	 	$this->negativeSynergy(array(1, 2, 3, 5, 7, 8, 28, 61), 2);
    	 	$this->negativeSynergy(array(6, 60, 65, 69, 71, 72), 1.5);
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
	* Excludes sour/smoked beers from recommended styles if user says NO
	*/
	private function excludeFromRecommended(array $ids_to_exclude) : void {
		foreach ($ids_to_exclude AS $id) {
			$this->included_ids[$id] = 0;
		}
	}

	/**
	* Prevents beers to be both included and excluded
	*/
	private function checkDoubles() : void {

		$included = array_slice($this->included_ids, 0, $this->cnt_styles_to_pick, true);
		$excluded = array_slice($this->excluded_ids, 0, $this->cnt_styles_to_avoid, true);

		foreach ($included AS $id => $points) {
			if (array_key_exists($id, $excluded)) {
				unset($this->included_ids[$id]);
			}
		}

	}

	/**
	* There must at least 125% margin between included and excluded beer
	* included > excluded
	*/
	private function checkMargin() : void {

		foreach ($this->included_ids AS $id => $points) {
			if (array_key_exists($id, $this->excluded_ids)) {
				$excluded_points = $this->excluded_ids[$id];
				$included_points = $points;
				if ($included_points > $excluded_points && $included_points <= $excluded_points * 1.25) {
					unset($this->excluded_ids[$id]);
				} 
			}
		}
	}

	/**
	* If there's an 4th and 5rd style with a little "margin" to 3rd style
	* Takes 4th or 5th style as an extra styles to take or avoid
	* TODO: Do jednej zmiennej pakować i w widoku 4-5 styl pokazywać inaczej
	*/
	private function optionalStyles() : void {

		$third_style_take = array_values(array_slice($this->included_ids, 0, 3, true));
		$third_style_avoid = array_values(array_slice($this->excluded_ids, 0, 3, true));

		for ($i = 3; $i <= 4; $i++) {

			$to_take_chunk = array_values(array_slice($this->included_ids, 0, $i, true));
			$to_avoid_chunk = array_values(array_slice($this->excluded_ids, 0, $i, true));
			
			if ($to_take_chunk[0] >= ($third_style_take[0] / 100 * 90)) {
				$this->cnt_styles_to_pick++;
			}

			if ($to_avoid_chunk[0] >= ($third_style_avoid[0] / 100 * 90)) {
				$this->cnt_styles_to_avoid++;
			}
		}

	}

	/**
	* If 1st styles to take and avoid has more than/equal 150% points of 2nd or 3rd styles
	* Emphasize them!
	*/
	private function mustTakeMustAvoid() : void {

		$first_style_take = array_values(array_slice($this->included_ids, 0, 1, true));
		$first_style_avoid = array_values(array_slice($this->excluded_ids, 0, 1, true));

		$second_style_take = array_values(array_slice($this->included_ids, 1, 1, true));
		$second_style_avoid = array_values(array_slice($this->excluded_ids, 1, 1, true));

		$third_style_take = array_values(array_slice($this->included_ids, 2, 1, true));
		$third_style_avoid = array_values(array_slice($this->excluded_ids, 2, 1, true));

		if ($second_style_take[0] * 1.25 <= $first_style_take[0] || $third_style_take[0] * 1.25 <= $first_style_take[0]) {
			$this->must_take = true;
		}

		if ($second_style_avoid[0] * 1.25 <= $first_style_avoid[0] || $third_style_avoid[0] * 1.25 <= $first_style_avoid[0]) {
			$this->must_avoid = true;
		}

	}


	/**
	* Remove points assigned to beer ids
	*/
	private function removePoints() {

		$this->included_ids = ($this->shuffled === false) ? array_keys($this->included_ids) : array_values($this->included_ids);
		$this->excluded_ids = array_keys($this->excluded_ids);

	}


	/*
	* Shuffle n-elements of an included_ids array
	*/
	private function shuffleStyles($toshuffle) {

		$this->included_ids = array_keys(array_slice($this->included_ids, 0, $toshuffle, true));
		shuffle($this->included_ids);

	}


	/**
	* Checks how many styles should be shuffled.
	* Margin between first and n-th style should be less than 90% of points).
	*/
	 private function checkShuffleStyles() : void {

		$first_style_index = key(array_slice($this->included_ids, 0, 1, true));
		$first_style_pts = array_values(array_slice($this->included_ids, 0, 1, true));

		$toshuffle = 0;

		for ($i = 1; $i <= count($this->included_ids); $i++) {

			$nth_style_index = key(array_slice($this->included_ids, $i, 1, true));
			$nth_style_pts = array_values(array_slice($this->included_ids, $i, 1, true));

			if ($nth_style_pts[0] >= $first_style_pts[0] * 0.90) {
				//echo $nth_style_pts[0] . ' >= ' . $first_style_pts[0] * 0.80;
				$toshuffle++;
			}

		}

		if ($toshuffle > 4) {
			$this->shuffleStyles($toshuffle);
			$this->shuffled = true;
		}

	} 
    
    /**
    * Heart of an algorithm
    */
    public function includeBeerIds(string $answers, string $name, string $email, int $newsletter) {

    	$answers_decoded = $this->answers_decoded = json_decode($answers);

    	foreach ($answers_decoded AS $number => $answer) {
	    	foreach ($this->{'to_include'.$number} AS $yesno => $ids) {

	    		if ($_POST['answer-15'] == 'tak') {
	    			$this->BA = true;
	    		} else {
	    			$this->BA = false;
	    		}

	    		//TODO: Refactor
	    		if ($_POST['answer-13'] == 'nie ma mowy') {
	    			$to_excl = array(40, 42, 44, 51, 56);
	    			$this->excludeFromRecommended($to_excl);
	    		}
	    		if ($_POST['answer-14'] == 'nie') {
	    			$to_excl = array(15, 16, 52, 57, 58, 59, 62, 63);
	    			$this->excludeFromRecommended($to_excl);
	    		}

	    		// Nie idź dalej przy BA
	    		if (in_array($ids, array('tak', 'nie'))) {
	    			continue;
	    		}

	    		$ids_to_calc = $this->strengthBuilder($ids);
	    		if ($answer == $yesno && $answer != 'bez znaczenia') {
		    		foreach ($ids_to_calc AS $style_id => $strength) {
		    			if (is_numeric($style_id)) {
		    				$this->included_ids[$style_id] += $strength;
		    			}
		    		}
	    		}

	    		if ($answer != $yesno && !in_array($number, array(3,5,9)) && $answer != 'bez znaczenia') { 
		    		foreach ($ids_to_calc AS $style_id => $strength) {
		    			if (is_numeric($style_id)) {
		    				$this->excluded_ids[$style_id] += $strength;
		    			}
		    		}
	    		} 

	    	}
    	}

    	$answer_value = get_object_vars($answers_decoded);
    	$this->synergyExecuter($answer_value);

    	return $this->chooseStyles($name, $email, $newsletter);

    }

    public function chooseStyles(string $name, string $email, int $newsletter) {
    		
   		arsort($this->included_ids);
    	arsort($this->excluded_ids);
    	$this->optionalStyles();
    	$this->checkDoubles();
		$this->checkMargin();
		$this->mustTakeMustAvoid();

    	if ($_SERVER['REMOTE_ADDR'] == '89.64.48.176') {
			$this->checkShuffleStyles();
    		$this->removePoints();
    		echo "<br /><br /><br />";
	    	echo "Tablica ze stylami do wybrania i punktami: <br />";
	    	$this->printPre($this->included_ids);
	    	echo "<br />Tablica ze stylami do odrzucenia i punktami: <br />";
	    	$this->printPre($this->excluded_ids);
    	}

    	for ($i = 0; $i < $this->cnt_styles_to_pick; $i++) {
    		$style_to_take = $this->style_to_take[] = $this->included_ids[$i];
    		$buythis[] = DB::select("SELECT * FROM beers WHERE id = $style_to_take");
    	}   	


    	for ($i = 0; $i < $this->cnt_styles_to_avoid; $i++) {
    		$style_to_avoid = $this->style_to_avoid[] = $this->excluded_ids[$i];
    		$avoidthis[] = DB::select("SELECT * FROM beers WHERE id = $style_to_avoid");	
    	}

    	//TODO
    	try {
    		$this->logStyles($name, $email, $newsletter);
    	} catch (Exception $e) {
    		//mail('kontakt@piwolucja.pl', 'logStyles Exception', $e->getMessage());
    	}

    	foreach ($this->style_to_take AS $index => $id) {
    		if (PKAPI::getBeerInfo($id) != null) {
    			$PK_style_take[] = PKAPI::getBeerInfo($id);
    		} else {
    			$PK_style_take[] = '';
    		}
    	}

    	return view('results', ['buythis' => $buythis, 'avoidthis' => $avoidthis, 'must_take' => $this->must_take, 'must_avoid' => $this->must_avoid, 'PK_style_take' => $PK_style_take, 'username' => $name, 'barrel_aged' => $this->BA, 'answers' => $this->answers_decoded]);

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
