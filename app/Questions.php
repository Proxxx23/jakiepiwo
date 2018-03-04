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

		// // Jeśli pił, to wykluczmy to, co pił
		// 4 => array("question" => "Wykluczamy to, co już znasz?", "type" => 0, "answers" => array('NO')),

		// // AIPA / Porter / Imperial Stout / Piwa kwaśne etc.

		// // Pytania techniczne
		// 5 => array("question" => "Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego?", "type" => 0, "answers" => array('NO')),
		// 6 => array("question" => "Jak mocne ma być?", "type" => 1, "answers" => array('wodniste', 'leciutkie', 'przeciętne', 'mocniejsze', 'tęgie', 'krew czorta')),
		// 7 => array("question" => "Wolisz jasne czy ciemne?", "type" => 0, "answers" => array('NO')),
		// 8 => array("question" => "Jak wysoką goryczkę tolerujesz?", "type" => 1, "answers" => array('ledwie wyczuwalną', 'delikatną', 'wyczuwalną', 'zdecydowanie wyczuwalną', 'mocną', 'jestem hopheadem')),
		// 9 => array("question" => "Raczej słodkie?", "type" => 0, "answers" => array('NO')),

		// // Pytania smakowe - podaj w skali
		// 10 => array("question" => "Czekoladowe lub palone?", "type" => 0, "answers" => array('NO')),
		// 11 => array("question" => "Lubisz torfową whisky (Islay)?", "type" => 0, "answers" => array('NO')),
		// 12 => array("question" => "Palone?", "type" => 0, "answers" => array('NO')), // Coś innego
		// 13 => array("question" => "Lubisz smak owoców w piwie?", "type" => 0, "answers" => array('NO')),
		// 14 => array("question" => "Co powiesz na piwo kwaśne?", "type" => 0, "answers" => array('NO')),
		// 15 => array("question" => "Co powiesz na piwo słonawe?", "type" => 0, "answers" => array('NO'))

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