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
		1 => array("question" => "Czy smakują Ci piwa koncernowe dostępne w sklepach?", "type" => 0, "answers" => array('NO')),
		2 => array("question" => "Czy chcesz poznać nowe piwne smaki?", "type" => 0, "answers" => array('NO')),
		3 => array("question" => "Czy wolałbyś poznać wyłącznie style piwne, które potrafią zszokować?", "type" => 0, "answers" => array('NO')),

		// TODO: Obsługa
		// 4 => array("question" => "Wykluczamy to, co już znasz?", "type" => 0, "answers" => array('NO')),
		// AIPA / Porter / Imperial Stout / Piwa kwaśne etc.

		//Inne niż tak/nie
		4 => array("question" => "Wolisz lekkie piwo do ugaszenia pragnienia, czy piwo bardziej złożone i degustacyjne?", "type" => 1, "answers" => array('coś lekkiego', 'coś pośrodku', 'coś złożonego')),
		5 => array("question" => "Jak wysoką goryczkę preferujesz?", "type" => 1, "answers" => array('ledwie wyczuwalną', 'lekką', 'zdecydowanie wyczuwalną', 'mocną', 'jestem hopheadem')),
		6 => array("question" => "Smakują Ci bardziej piwa jasne, czy piwa ciemne?", "type" => 1, "answers" => array('jasne', 'bez znaczenia', 'ciemne')),
		7 => array("question" => "Smakują Ci bardziej piwa słodsze, czy piwa wytrawniejsze?", "type" => 1, "answers" => array('słodsze', 'bez znaczenia', 'wytrawniejsze')),

		// Pytania smakowe - podaj w skali
		8 => array("question" => "Czy odpowiadałby Ci smak czekoladowy w piwie?", "type" => 0, "answers" => array('NO')),
		9 => array("question" => "Czy wolisz piwa mocno nagazowane?", "type" => 0, "answers" => array('NO')),
		10 => array("question" => "Czy odpowiadałby Ci smak palony w piwie?", "type" => 0, "answers" => array('NO')), // Coś innego
		11 => array("question" => "Czy chciałbyś piwo w klimatach owocowych (bez soku)?", "type" => 0, "answers" => array('NO')),
		12 => array("question" => "Co powiesz na piwo kwaśne?", "type" => 0, "answers" => array('NO')),
		13 => array("question" => "Co powiesz na piwo słonawe?", "type" => 0, "answers" => array('NO')),
		14 => array("question" => "Czy lubisz bourbon, whisky lub inne alkohole szlachetne?", "type" => 0, "answers" => array('NO')),

		// Dokładne dodatkowe 5 pytań


	);

	// Dodatkowe pytania dokładne do przeniesienia wyżej
	public static $accurate_questions = array(

		0 => array("question" => "Dodatkowe pytanie nr", "type" => 0),
		1 => array("question" => "Dodatkowe pytanie nr", "type" => 0),
		2 => array("question" => "Dodatkowe pytanie nr", "type" => 0),
		3 => array("question" => "Dodatkowe pytanie nr", "type" => 0),
		4 => array("question" => "Dodatkowe pytanie nr", "type" => 0)

	);

}