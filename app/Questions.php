<?php
declare(strict_types=1);
namespace App\Traits;

/**
 * Trait Questions
 * @package App\Traits
 */
trait Questions {
	
	public static $questions = [
		1 => array("question" => "Czy smakują Ci piwa koncernowe dostępne w sklepach?", "type" => 0, "answers" => array('NO'), "tooltip" => "Chodzi o jasne piwa koncernowe, takie jak Lech, Kasztelan, Tyskie czy Specjal."),
		2 => array("question" => "Czy chcesz poznać nowe piwne smaki?", "type" => 0, "answers" => array('NO')),
		3 => array("question" => "Czy wolałbyś poznać wyłącznie style piwne, które potrafią zszokować?", "type" => 0, "answers" => array('NO')),

		4 => array("question" => "Wolisz lekkie piwo do ugaszenia pragnienia, czy piwo bardziej złożone i degustacyjne?", "type" => 1, "answers" => array('coś lekkiego', 'coś pośrodku', 'coś złożonego'), "tooltip" => "Piwa lekkie oferują mniejszą głębię, zaś piwa złożone to większe nagromadzenie różnych aromatów i smaków."),
		5 => array("question" => "Jak wysoką goryczkę preferujesz?", "type" => 1, "answers" => array('ledwie wyczuwalną', 'lekką', 'zdecydowanie wyczuwalną', 'mocną', 'jestem hopheadem')),
		6 => array("question" => "Smakują Ci bardziej piwa jasne, czy piwa ciemne?", "type" => 1, "answers" => array('jasne', 'bez znaczenia', 'ciemne')),
		7 => array("question" => "Smakują Ci bardziej piwa słodsze, czy piwa wytrawniejsze?", "type" => 1, "answers" => array('słodsze', 'bez znaczenia', 'wytrawniejsze')),

		8 => array("question" => "Jak mocne i gęste piwa preferujesz?", "type" => 1, "answers" => array('wodniste i lekkie', 'średnie', 'mocne i gęste')),
		9 => array("question" => "Czy odpowiadałby Ci smak czekoladowy w piwie?", "type" => 0, "answers" => array('NO')),
		10 => array("question" => "Czy odpowiadałby Ci smak kawowy w piwie?", "type" => 0, "answers" => array('NO')),
		11 => array("question" => "Czy odpowiadałoby Ci piwo nieco przyprawowe?", "type" => 0, "answers" => array('NO'), "tooltip" => "W niektórych piwach da się wyczuć na przykład goździki czy nuty pieprzowe."),
		12 => array("question" => "Czy chciałbyś piwo w klimatach owocowych?", "type" => 0, "answers" => array('NO'), "tooltip" => "Chodzi o piwa bez dodatku soku, w których nuty owocowe otrzymano dzięki użyciu odpowiednich odmian chmielu lub dzięki pracy drożdży."),
		13 => array("question" => "Co powiesz na piwo kwaśne?", "type" => 1, "answers" => array('chętnie', 'nie ma mowy'), "tooltip" => "Kwaśne nie oznacza, że piwo jest zepsute czy stare."),
		14 => array("question" => "Czy odpowiadałby Ci smak wędzony/dymny w piwie?", "type" => 0, "answers" => array('NO')),
		15 => array("question" => "Czy lubisz bourbon, whisky lub inne alkohole szlachetne?", "type" => 0, "answers" => array('NO')),
	];

}