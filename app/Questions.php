<?php
declare(strict_types=1);
namespace App\Traits;

trait Questions {
 
	/*@type
	* 0 - pytanie "tak/może/nie"
	* 1 - pytanie w skali
	*/
	
	public static $questions = array(

		// Pytania wstępne
		1 => array("question" => "Czy smakują Ci lekkie piwa koncernowe dostępne w sklepach?", "type" => 0, "answers" => array('NO')),
		2 => array("question" => "Czy chcesz poznać nowe smaki?", "type" => 0, "answers" => array('NO')),
		3 => array("question" => "Czy piłeś już nietypowe piwa?", "type" => 0, "answers" => array('NO')),

		// TODO: Obsługa
		// 4 => array("question" => "Wykluczamy to, co już znasz?", "type" => 0, "answers" => array('NO')),
		// AIPA / Porter / Imperial Stout / Piwa kwaśne etc.

		// Pytania techniczne
		4 => array("question" => "Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego?", "type" => 0, "answers" => array('NO')),
		5 => array("question" => "Jak mocne ma być?", "type" => 1, "answers" => array('wodniste', 'leciutkie', 'przeciętne', 'mocniejsze', 'tęgie', 'krew czorta')),
		6 => array("question" => "Wolisz jasne czy ciemne?", "type" => 0, "answers" => array('NO')),
		7 => array("question" => "Jak wysoką goryczkę tolerujesz?", "type" => 1, "answers" => array('ledwie wyczuwalną', 'delikatną', 'wyczuwalną', 'zdecydowanie wyczuwalną', 'mocną', 'jestem hopheadem')),
		8 => array("question" => "Raczej słodkie?", "type" => 0, "answers" => array('NO')),

		// Pytania smakowe - podaj w skali
		9 => array("question" => "Klimaty czekoladowe?", "type" => 0, "answers" => array('NO')),
		10 => array("question" => "Lubisz torfową whisky (Islay)?", "type" => 0, "answers" => array('NO')),
		11 => array("question" => "Odpowiada Ci palony smak?", "type" => 0, "answers" => array('NO')), // Coś innego
		12 => array("question" => "Bardziej owocowo?", "type" => 0, "answers" => array('NO')),
		13 => array("question" => "Co powiesz na piwo kwaśne?", "type" => 0, "answers" => array('NO')),
		14 => array("question" => "Co powiesz na piwo słonawe?", "type" => 0, "answers" => array('NO'))

		// Dokładne dodatkowe 5 pytań

	);

	// Dodatkowe pytania dokładne
	public static $accurate_questions = array(

		0 => array("question" => "Dokładne pytanie", "type" => 0),
		1 => array("question" => "Dokładne pytanie", "type" => 0),
		2 => array("question" => "Dokładne pytanie", "type" => 0),
		3 => array("question" => "Dokładne pytanie", "type" => 0),
		4 => array("question" => "Dokładne pytanie", "type" => 0)

	);

}