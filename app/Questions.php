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
		3 => array("question" => "Czy wolałbyś poznać wyłącznie style, które potrafią zszokować?", "type" => 0, "answers" => array('NO')),

		// TODO: Obsługa
		// 4 => array("question" => "Wykluczamy to, co już znasz?", "type" => 0, "answers" => array('NO')),
		// AIPA / Porter / Imperial Stout / Piwa kwaśne etc.

		//Inne niż tak/nie
		4 => array("question" => "Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego?", "type" => 1, "answers" => array('coś lekkiego', 'coś pośrodku', 'coś złożonego')),
		5 => array("question" => "Jak wysoką goryczkę tolerujesz?", "type" => 1, "answers" => array('ledwie wyczuwalną', 'lekką', 'zdecydowanie wyczuwalną', 'mocną', 'jestem hopheadem')),
		6 => array("question" => "Wolisz jasne czy ciemne?", "type" => 1, "answers" => array('jasne', 'bez znaczenia', 'ciemne')),
		7 => array("question" => "Raczej słodkie?", "type" => 1, "answers" => array('słodsze', 'bez znaczenia', 'wytrawniejsze')),

		// Pytania smakowe - podaj w skali
		8 => array("question" => "Klimaty czekoladowe?", "type" => 0, "answers" => array('NO')),
		9 => array("question" => "Gęsta konsystencja?", "type" => 0, "answers" => array('NO')),
		10 => array("question" => "Odpowiada Ci palony smak?", "type" => 0, "answers" => array('NO')), // Coś innego
		11 => array("question" => "Bardziej owocowo?", "type" => 0, "answers" => array('NO')),
		12 => array("question" => "Co powiesz na piwo kwaśne?", "type" => 0, "answers" => array('NO')),
		13 => array("question" => "Co powiesz na piwo słonawe?", "type" => 0, "answers" => array('NO'))

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