<?php
declare(strict_types=1);
namespace App\Traits;

trait Questions {
 
	/*@type
	* 0 - pytanie "tak/nie"
	* 1 - pytanie <> tak/nie
	*/
	
	public static $questions = array(

		// Pytania wstępne
		1 => array("question" => "Czy smakują Ci piwa koncernowe dostępne w sklepach?", "type" => 0, "answers" => array('NO'), "tooltip" => "Chodzi o jasne piwa koncernowe, takie jak Lech, Kasztelan, Tyskie czy Specjal."),
		2 => array("question" => "Czy chcesz poznać nowe piwne smaki?", "type" => 0, "answers" => array('NO')),
		3 => array("question" => "Czy wolałbyś poznać wyłącznie style piwne, które potrafią zszokować?", "type" => 0, "answers" => array('NO')),

		// TODO: Obsługa
		// 4 => array("question" => "Wykluczamy to, co już znasz?", "type" => 0, "answers" => array('NO')),
		// AIPA / Porter / Imperial Stout / Piwa kwaśne etc.

		//Inne niż tak/nie
		4 => array("question" => "Wolisz lekkie piwo do ugaszenia pragnienia, czy piwo bardziej złożone i degustacyjne?", "type" => 1, "answers" => array('coś lekkiego', 'coś pośrodku', 'coś złożonego'), "tooltip" => "Piwa lekkie oferują mniejszą głębię, zaś piwa złożone to większe nagromadzenie różnych aromatów i smaków."),
		5 => array("question" => "Jak wysoką goryczkę preferujesz?", "type" => 1, "answers" => array('ledwie wyczuwalną', 'lekką', 'zdecydowanie wyczuwalną', 'mocną', 'jestem hopheadem')),
		6 => array("question" => "Smakują Ci bardziej piwa jasne, czy piwa ciemne?", "type" => 1, "answers" => array('jasne', 'bez znaczenia', 'ciemne')),
		7 => array("question" => "Smakują Ci bardziej piwa słodsze, czy piwa wytrawniejsze?", "type" => 1, "answers" => array('słodsze', 'bez znaczenia', 'wytrawniejsze')),

		// Pytania smakowe - podaj w skali
		8 => array("question" => "Czy odpowiadałby Ci smak czekoladowy w piwie?", "type" => 0, "answers" => array('NO')),
		9 => array("question" => "Czy wolisz piwa mocno gazowane?", "type" => 0, "answers" => array('NO')),
		10 => array("question" => "Czy odpowiadałoby Ci piwo nieco przyprawowe?", "type" => 0, "answers" => array('NO'), "tooltip" => "W niektórych piwach da się wyczuć na przykład goździki czy nuty pieprzne."), // TODO: Zamienić z 9 miejscami
		11 => array("question" => "Czy chciałbyś piwo w klimatach owocowych?", "type" => 0, "answers" => array('NO'), "tooltip" => "Chodzi o piwa bez dodatku soku, w których nuty owocowe otrzymano dzięki użyciu odpowiednich odmian chmielu lub dzięki pracy drożdży."),
		12 => array("question" => "Co powiesz na piwo kwaśne?", "type" => 1, "answers" => array('chętnie', 'nie ma mowy'), "tooltip" => "Kwaśne nie oznacza, że piwo jest zepsute czy stare."),
		13 => array("question" => "Czy odpowiadałby Ci smak wędzony/dymny w piwie?", "type" => 0, "answers" => array('NO')),

		14 => array("question" => "Czy lubisz bourbon, whisky lub inne alkohole szlachetne?", "type" => 0, "answers" => array('NO')),
		15 => array("question" => "Jak mocne i gęste piwa preferujesz?", "type" => 1, "answers" => array('wodniste i lekkie', 'średnie', 'mocne i gęste')),
	);

}