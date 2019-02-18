<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PolskiKraft\PolskiKraftAPI AS PKAPI;
use Illuminate\View\View;

/**
 * Class PickingAlgorithm
 * @package App\Http\Controllers
 */
class PickingAlgorithm extends Controller
{
    /**
     * Array zawiera pary 'odpowiedź' => id_piw z bazy do zaliczenia w przypadku wyboru tej odpowiedzi + ew. dodatkowa siła
     */
    // Czy smakują Ci lekkie piwa koncernowe dostępne w sklepach?
    /** @var array */
    protected $toInclude1 = [
        'tak' => '9:2,10:2,11:2,12:2,13:2,14:2,25:2,27:2,45:2,52:2,68:2,70:2,72:2,76:2',
        'nie' => '5,6,7,8,22,23,24,28,30,32,33,34,35,36,37,38,39,40,42,44,47,48,49,50,51,53,55,56,57,58,59,60,61,62,63,64,65,67,69,71,73,74,75',
    ];

    // Czy chcesz poznać nowe smaki?
    /** @var array */
    protected $toInclude2 = [
        'tak' => '1,2,3,4,5,6,7,8,15,16,19,20,22,23,24,28,30,32,33,34,35,36,37,38,39,40,42,44,45,47,49,50,51,52,53,55,56,57,58,59,60,61,62,63,64,65,67,69,70,71,72,73,74,75',
        'nie' => '9:2,10:2,11:2,12:1.5,13:2,14,21,25,27:0.5,48:0:5,68,72:2',
    ];

    // Czy wolałbyś poznać wyłącznie style, które potrafią zszokować?
    /** @var array */
    protected $toInclude3 = [
        'tak' => '1:1.5,2:2.5,3:2.5,5:2.5,6:2.5,7:2.5,8:2.5,15:2.5,16:2.5,23:2.5,24:2.5,36:2.5,37:2.5,40:2.5,42:2.5,44:2.5,50:2.5,51:2.5,55:2.5,56:2.5,57:2.5,58:2.5,59:2.5,60:1.5,61:1.5,62:2.5,63:2.5,73:2.5,74:2.5,75:2.5',
        'nie' => '',
    ];

    // Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego?
    /** @var array */
    protected $toInclude4 = [
        'coś lekkiego' => '9,10,11,12,13,21,25,32,33,40,45,47,51,52',
        'coś pośrodku' => '14,15,16,19,20,25,27,28,30,34,35,38,42,44,45,47,48,49,53,55,56,57,58,59,60,61,64',
        'coś złożonego' => '1,2,3,5,6,7,8,22,23,24,36,37,39,42,44,50,62,63',
    ];

    // Jak wysoką goryczkę preferujesz?
    /** @var array */
    protected $toInclude5 = [
        'ledwie wyczuwalną' => '9,14,15,16,19,20,25,40,44,45,50,51,53,56',
        'lekką' => '11,12,21,22,23,34,42,45,47,48,49,50,52,55,57,59,60',
        'zdecydowanie wyczuwalną' => '1,2,3,5,6,7,8,10,13,21,24,27,28,30,32,33,35,38,39,55,57,58,59,60,61,62,63,64',
        'mocną' => '1,2,3,5,6,7,8,35,36,37,58,59,62,63',
        'jestem hopheadem' => '1,3,5,7,8',
    ];

    // Wolisz piwa jasne czy ciemne?
    /** @var array */
    protected $toInclude6 = [
        'jasne' => '1:2.5,2:2.5,5:2.5,6:2.5,7:2.5,8:2.5,9:2.5,10:2.5,11:2.5,13:2.5,14:2.5,15:2.5,16:2.5,20:2.5,22:2.5,23:2.5,25:2.5,27:2.5,28:2.5,32:2.5,38:2.5,39:2.5,40:2.5,42:2.5,44:2.5,45:2.5,47:2.5,49:2.5,50:2.5,51:2.5,52:2.5,53:2.5,55:2.5,56:2.5,57:2.5,60:2.5,61:2.5,65:2.5,67:2.5,68:2.5,69:2.5,70:2.5,72:2.5,73:2.5',
        'bez znaczenia' => '',
        'ciemne' => '3:2.5,12:2.5,19:2.5,21:2.5,24:2.5,30:2.5,33:2.5,34:2.5,35:2.5,36:2.5,37:2.5,48:2.5,58:2.5,59:2.5,62:2.5,63:2.5,64:2.5,71:2.6,47:2.5,75:2.5,76:2.5',
    ];

    // Wolisz piwa słodsze czy wytrawniejsze?
    /** @var array */
    protected $toInclude7 = [
        'słodsze' => '1,2,5,6,7:1.5,8,14:1.5,15:1.5,16:1.5,19,20:1.5,22:2,23:2,25,34:2,36,38,39:1.5,49:1.5,50,53,60:1.5,62,63,67:2.5,68,69,73:2,75,76',
        'bez znaczenia' => '',
        'wytrawniejsze' => '3:1.5,5,9,10:1.5,11,12,13:1.5,21,28,33:2,35:2,36,37,40,45,47,48,52:1.5,55,57,58,59,61,62,63,64,65,70,71,72,74,75',
    ];
    // Jak mocne i gęste piwa preferujesz?
    /** @var array */
    protected $toInclude8 = [
        'wodniste i lekkie' => '9:4,10:4,11:4,12:4,13:4,33:4,44:4,51:4,52:4,64:4,45:4,64:4,68:4,70:4,72:4',
        'średnie' => '1:4,2:4,3:4,5:4,6:4,7:4,14:4,15:4,16:4,19:4,21:4,25:4,27:4,28:4,29:4,30:4,32:4,34:4,38:4,42:4,47:4,48:4,53:4,55:4,56:4,57:4,59:4,60:4,61:4,64:4,69:4,71:4,72:4,73:4,74:4,75:4,75:5',
        'mocne i gęste' => '7:4,8:4,20:4,22:4,23:4,24:4,36:4,37:4,39:4,49:4,50:4,53:4,58:4,62:4,63:4,67:4,74:2,75:2',
    ];
    //
    // Czy odpowiadałby Ci smak czekoladowy w piwie?
    /** @var array */
    protected $toInclude9 = [
        'tak' => '3:1.5,12:1.5,21:1.5,24:2,30,33:1.5,34:2,35:2,36:2,37:2,48,58:2,59:1.5,62:2,63:2,71:1.5,74,75:1.5',
        'nie' => '1,2,5,6,7,8,9,10,11,13,14,15,16,19,20,22,23,25,27,28,32,38,39,40,42,44,45,47,49,50,51,52,53,55,56,57,60,61,64,65,67,68,69,70,72,73,76',
    ];

    // Czy odpowiadałby Ci smak kawowy w piwie?
    /** @var array */
    protected $toInclude10 = [
        'tak' => '3,24,30,33,34,35,36,37,58,59,62,63,71,74:3,75',
        'nie' => '1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,25,27,28,30,32,38,39,40,42,44,45,46,47,48,49,50,51,52,53,55,56,57,60,61,64,65,67,68,69,70,72,73,76',
    ];

    // Czy odpowiadałoby Ci piwo nieco przyprawowe
    /** @var array */
    protected $toInclude11 = [
        'tak' => '2:1.5,20,25,45:1.5,47:1.5,48,49:2,50:2,53:1.5,67:1.5,68',
        'nie' => '',
    ];

    // Czy chciałbyś piwo w klimatach owocowych (bez soku)?
    /** @var array */
    protected $toInclude12 = [
        'tak' => '1:2,2:1.5,5:1.5,6:1.5,7:2,8:2,25,40:1.5,42:1.5,44,45,47:0.5,49:1.5,50,51:1.25,55,56:1.5,60:2,61:2,65:1.5,67,69:2,70:1.5,72,73:2.5,76',
        'nie' => '3,9,10,11,12,13,14,15,16,19,20,21,22,23,24,27,28,30,32,33,34,35,36,37,38,39,47,48,52,53,57,58,59,62,63,64,68,71,74,75',
    ];

    // Co powiesz na piwo kwaśne?
    /** @var array */
    protected $toInclude13 = [
        'tak' => '40:2,42:3,44:3,51:2,56:2',
        'nie' => '1,2,3,5,6,7,8,9,10,11,12,13,14,15,16,19,20,21,22,23,24,25,27,28,30,32,33,34,35,36,37,38,39,40,45,47,48,49,50,52,53,55:0.5,57,58,59,60,61,62,63,64,65,67,68,69,70,71,72,73,74,75,76',
    ];

    // Czy odpowiadałby Ci smak wędzony/dymny w piwie?
    /** @var array */
    protected $toInclude14 = [
        'tak' => '15:1.5,16:1.5,52:1.5,58:1.5,59:1.5,62:1.5,63:1.5',
        'nie' => '',
    ];

    // BA
    /** @var array */
    protected $toInclude15 = [
        'tak' => 'tak',
        'nie' => 'nie',
    ];

    /** @var array */
    public $answersDecoded = [];

    /** @var array */
    private $includedIds = []; // Beer IDs to include
    /** @var array */
    private $excludedIds = []; // Excluded beer IDs
    /** @var array */
    private $styleToTake = []; // Styles user should buy
    /** @var array */
    private $styleToAvoid = []; // Styles user should avoid

    /** @var bool */
    private $mustTake = false; // TODO: Obsługa wszystkich 3 stylów
    /** @var bool */
    private $mustAvoid = false; // TODO: Obsługa wszystkich 3 stylów

    /** @var bool */
    public $barrelAged = false; // BA beers
    /** @var bool */
    private $shuffled = false;

    /** @var int */
    private $countStylesToTake = 3;
    /** @var int */
    private $countStylesToAvoid = 3;

    /**
     * Builds positive synergy if user ticks 2-4 particular answers
     * TODO: Synergy Class
     *
     * @param array $idsToMultiply
     * @param float $multiplier
     */
    private function positiveSynergy(array $idsToMultiply, float $multiplier): void
    {
        foreach ($idsToMultiply AS $id) {
            $this->includedIds[$id] *= $multiplier;
        }
    }

    /**
     * Builds negative synergy if user ticks 2-4 particular answers
     * TODO: Synergy Class
     *
     * @param array $idsToDivide
     * @param float $divider
     */
    private function negativeSynergy(array $idsToDivide, float $divider): void
    {
        foreach ($idsToDivide AS $id) {
            $this->excludedIds[$id] = floor($this->excludedIds[$id] / $divider);
        }
    }

    /**
     * Positive and negative synergies executer
     * There are all the synergies
     * TODO: Synergy Class
     *
     * @param array $answerValue
     */
    private function synergyExecuter(array $answerValue): void
    {

        // Lekkie + owocowe + Kwaśne
        if ($answerValue[4] === 'coś lekkiego' &&
            $answerValue[12] === 'tak' &&
            $answerValue[13] === 'chętnie') {
            echo 'Synergia Lekkie + owocowe + Kwaśne <br />';
            $this->positiveSynergy([40, 56], 2);
            $this->positiveSynergy([51], 1.5);
        }
        // nowe smaki LUB szokujące + złożone + jasne
        if ($answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'jasne' &&
            ($answerValue[2] === 'tak' || $answerValue[3] === 'tak')) {
            echo 'Synergia we smaki LUB szokujące + złożone + jasne <br />';
            $this->positiveSynergy([7, 15, 16, 23, 39, 42, 50, 60, 73], 2);
        }

        // nowe smaki LUB szokujące + złożone + ciemne
        if ($answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'ciemne' &&
            ($answerValue[2] === 'tak' || $answerValue[3] === 'tak')) {
            echo 'Synergia nowe smaki LUB szokujące + złożone + ciemne <br />';
            $this->positiveSynergy([36, 37, 58, 59, 62, 63], 2);
        }

        // złożone + ciemne + nieowocowe
        if ($answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[12] === 'nie') {
            echo 'Synergia złożone + ciemne + nieowocowe <br />';
            $this->positiveSynergy([3, 24, 35, 36, 37, 48, 58, 59, 62, 63, 75], 1.5);
        }

        // złożone + ciemne + nieowocowe + kawowe
        if ($answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[10] === 'tak' &&
            $answerValue[12] === 'nie') {
            echo 'Synergia złożone + ciemne + nieowocowe + kawowe <br />';
            $this->positiveSynergy([74], 2.5);
        }

        // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna)
        if ($answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[7] === 'słodsze' &&
            !in_array($answerValue[5], ['mocną', 'jestem hopheadem'])) {
            echo 'Synergia Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna) <br />';
            $this->positiveSynergy([12, 29, 30, 34, 64], 2);
            $this->negativeSynergy([36, 37], 3);
        }

        // jasne + nieczekoladowe
        if ($answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie') {
            echo 'Synergia jasne + nieczekoladowe <br />';
            $this->negativeSynergy([12, 21, 24, 29, 33, 34, 35, 36, 37, 58, 59, 62, 63, 71, 74, 75], 2);
        }

        // ciemne + czekoladowe + lżejsze
        if ($answerValue[6] === 'ciemne' &&
            $answerValue[9] === 'tak' &&
            $answerValue[8] !== 'mocne i gęste') {
            echo 'Synergia ciemne + czekoladowe + lżejsze <br />';
            $this->positiveSynergy([12, 31, 33, 34, 35, 59, 71], 2.5);
        }


        // goryczka ledwo || lekka + jasne + nieczkoladowe + niegęste
        if ($answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' &&
            $answerValue[8] !== 'mocne i gęste' &&
            ($answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką')) {
            echo 'Synergia goryczka ledwo || lekka + jasne + nieczkoladowe + niegęste <br />';
            $this->positiveSynergy([20, 25, 40, 44, 45, 47, 51, 52, 53, 68, 73], 2);
            $this->negativeSynergy([3, 24, 35, 36, 37, 58, 59, 62, 63, 71, 75], 2);
        }

        // jasne + lekkie + wodniste + wędzone = grodziskie
        if ($answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'jasne' &&
            $answerValue[8] === 'wodniste' &&
            $answerValue[14] === 'tak') {
            echo 'Synergia asne + lekkie + wodniste + wędzone = grodziskie <br />';
            $this->positiveSynergy([52], 3);
            $this->negativeSynergy([3, 22, 23, 24, 35, 36, 37, 50, 58, 59, 62, 63, 71, 75], 2);
        }

        // duża/hophead goryczka + jasne
        if ($answerValue[6] === 'jasne' &&
            ($answerValue[5] === 'mocną' || $answerValue[5] === 'jestem hopheadem')) {
            echo 'Synergia duża/hophead goryczka + jasne <br />';
            $this->positiveSynergy([1, 2, 5, 6, 7, 8, 28, 61], 1.75);
            $this->positiveSynergy([65, 69, 70, 72], 1.5);
        }

        // duża/hophead goryczka + ciemne
        if ($answerValue[6] === 'ciemne' &&
            ($answerValue[5] === 'mocną' || $answerValue[5] === 'jestem hopheadem')) {
            echo 'Synergia duża/hophead goryczka + ciemne <br />';
            $this->positiveSynergy([3, 36, 37, 58, 62, 63, 75], 1.75);
        }

        // goryczka ledwo || lekka
        if ($answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką') {
            echo 'Synergia negatywna na lekkie goryczki <br />';
            $this->negativeSynergy([1, 2, 3, 5, 7, 8, 28, 61], 2);
            $this->negativeSynergy([6, 60, 65, 69, 71, 72], 1.5);
        }
    }

    /**
     * Buduje siłę dla konkretnych ID stylu
     * Jeśli id ma postać 5:2.5 to zwiększy (przy trafieniu w to ID) punktację tego stylu o 2.5 a nie o 1
     * Domyślnie zwiększa punktację stylu o
     *
     * @param string $ids
     *
     * @return array
     */
    private function strengthBuilder(string $ids): array
    {
        $idsExploded = explode(',', trim($ids));
        $strength = [];
        foreach ($idsExploded AS $v) {
            if (false !== strpos($v, ':') || false !== strpos($v, ' :')) {
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
     *
     * @param array $idsToExclude
     */
    private function excludeFromRecommended(array $idsToExclude): void
    {
        foreach ($idsToExclude AS $id) {
            $this->includedIds[$id] = 0;
        }
    }

    /**
     * Prevents beers to be both included and excluded
     */
    private function checkDoubles(): void
    {

        $included = array_slice($this->includedIds, 0, $this->countStylesToTake, true);
        $excluded = array_slice($this->excludedIds, 0, $this->countStylesToAvoid, true);

        foreach ($included AS $id => $points) {
            if (array_key_exists($id, $excluded)) {
                unset($this->includedIds[$id]);
            }
        }
    }

    /**
     * There must at least 125% margin between included and excluded beer
     * included > excluded
     */
    private function checkMargin(): void
    {
        foreach ($this->includedIds AS $id => $points) {
            if (array_key_exists($id, $this->excludedIds)) {
                $excludedPoints = $this->excludedIds[$id];
                $includedPoints = $points;
                if ($includedPoints > $excludedPoints && $includedPoints <= $excludedPoints * 1.25) {
                    unset($this->excludedIds[$id]);
                }
            }
        }
    }

    /**
     * If there's an 4th and 5rd style with a little 'margin" to 3rd style
     * Takes 4th or 5th style as an extra styles to take or avoid
     * TODO: Do jednej zmiennej pakować i w widoku 4-5 styl pokazywać inaczej
     */
    private function optionalStyles(): void
    {

        $thirdStyleToTake = array_values(array_slice($this->includedIds, 0, 3, true));
        $thirdStyleToAvoid = array_values(array_slice($this->excludedIds, 0, 3, true));

        for ($i = 3; $i <= 4; $i++) {

            $toTakeChunk = array_values(array_slice($this->includedIds, 0, $i, true));
            $toAvoidChunk = array_values(array_slice($this->excludedIds, 0, $i, true));

            if ($toTakeChunk[0] >= ($thirdStyleToTake[0] / 100 * 90)) {
                $this->countStylesToTake++;
            }

            if ($toAvoidChunk[0] >= ($thirdStyleToAvoid[0] / 100 * 90)) {
                $this->countStylesToAvoid++;
            }
        }

    }

    /**
     * If 1st styles to take and avoid has more than/equal 150% points of 2nd or 3rd styles
     * Emphasize them!
     */
    private function mustTakeMustAvoid(): void
    {

        $firstStyleToTake = array_values(array_slice($this->includedIds, 0, 1, true));
        $firstStyleToAvoid = array_values(array_slice($this->excludedIds, 0, 1, true));

        $secondStyleToTake = array_values(array_slice($this->includedIds, 1, 1, true));
        $secondStyleToAvoid = array_values(array_slice($this->excludedIds, 1, 1, true));

        $thirdStyleToTake = array_values(array_slice($this->includedIds, 2, 1, true));
        $thirdStyleToAvoid = array_values(array_slice($this->excludedIds, 2, 1, true));

        if ($secondStyleToTake[0] * 1.25 <= $firstStyleToTake[0] || $thirdStyleToTake[0] * 1.25 <= $firstStyleToTake[0]) {
            $this->mustTake = true;
        }

        if ($secondStyleToAvoid[0] * 1.25 <= $firstStyleToAvoid[0] || $thirdStyleToAvoid[0] * 1.25 <= $firstStyleToAvoid[0]) {
            $this->mustAvoid = true;
        }
    }

    /**
     * Remove points assigned to beer ids
     */
    private function removePoints(): void
    {
        $this->includedIds = ($this->shuffled === false)
            ? \array_keys($this->includedIds)
            : \array_values($this->includedIds);
        $this->excludedIds = \array_keys($this->excludedIds);
    }

    /**
     * Shuffle n-elements of an includedIds array
     *
     * @param $toShuffle
     */
    private function shuffleStyles($toShuffle): void
    {
        $this->includedIds = array_keys(array_slice($this->includedIds, 0, $toShuffle, true));

        \shuffle($this->includedIds);
    }

    /**
     * Checks how many styles should be shuffled.
     * Margin between first and n-th style should be less than 90% of points).
     */
    private function checkShuffleStyles(): void
    {

//		$firstStyleIndex = key(array_slice($this->includedIds, 0, 1, true));
        $firstStylePoints = array_values(array_slice($this->includedIds, 0, 1, true));

        $toShuffle = 0;
        $countIncluded = count($this->includedIds);

        for ($i = 1; $i <= $countIncluded; $i++) {
//			$nthStyleIndex = key(array_slice($this->includedIds, $i, 1, true));
            $nthStylePoints = array_values(array_slice($this->includedIds, $i, 1, true));

            if ($nthStylePoints[0] >= $firstStylePoints[0] * 0.90) {
                //echo $nthStylePoints[0] . ' >= ' . $firstStylePoints[0] * 0.80;
                $toShuffle++;
            }
        }

        if ($toShuffle > 4) {
            $this->shuffleStyles($toShuffle);
            $this->shuffled = true;
        }
    }

    /**
     * Heart of an algorithm
     *
     * @param string $answers
     * @param string $name
     * @param string $email
     * @param int $newsletter
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function includeBeerIds(string $answers, string $name, string $email, int $newsletter)
    {
        $answersDecoded = $this->answersDecoded = \json_decode($answers);

        foreach ($answersDecoded AS $number => &$answer) {
            foreach ($this->{'toInclude' . $number} AS $yesno => $ids) {

                if ($_POST['answer-15'] === 'tak') {
                    $this->barrelAged = true;
                } else {
                    $this->barrelAged = false;
                }

                if ($_POST['answer-13'] === 'nie ma mowy') {
                    $toExclude = [40, 42, 44, 51, 56];
                    $this->excludeFromRecommended($toExclude);
                }
                if ($_POST['answer-14'] === 'nie') {
                    $toExclude = [15, 16, 52, 57, 58, 59, 62, 63];
                    $this->excludeFromRecommended($toExclude);
                }

                // Nie idź dalej przy BA
                if (\in_array($ids, ['tak', 'nie'], true)) {
                    continue;
                }

                $idsToCalculate = $this->strengthBuilder($ids);
                if ($answer === $yesno && $answer !== 'bez znaczenia') {
                    foreach ($idsToCalculate AS $styleId => &$strength) {
                        if (\is_numeric($styleId)) {
                            $this->includedIds[$styleId] += $strength;
                        }
                    }
                    unset($strength);
                }

                if ($answer !== $yesno && $answer !== 'bez znaczenia' &&
                    !\in_array($number, [3, 5, 9], true)) {
                    foreach ($idsToCalculate AS $styleId => &$strength) {
                        if (\is_numeric($styleId)) {
                            $this->excludedIds[$styleId] += $strength;
                        }
                    }
                    unset($strength);
                }

            }
        }
        unset($answer);

        $answerValue = \get_object_vars($answersDecoded);
        $this->synergyExecuter($answerValue);

        return $this->chooseStyles($name, $email, $newsletter);
    }

    /**
     * @param string $name
     * @param string $email
     * @param int $newsletter
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chooseStyles(string $name, string $email, int $newsletter): View
    {

        arsort($this->includedIds);
        arsort($this->excludedIds);
        $this->optionalStyles();
        $this->checkDoubles();
        $this->checkMargin();
        $this->mustTakeMustAvoid();
        $this->checkShuffleStyles();

        if ($_SERVER['REMOTE_ADDR'] === '89.64.48.176') {
            echo '<br /><br /><br />';
            echo 'Tablica ze stylami do wybrania i punktami: <br />';
            $this->printPre($this->includedIds);
            echo '<br />Tablica ze stylami do odrzucenia i punktami: <br />';
            $this->printPre($this->excludedIds);
        }

        $this->removePoints();

        $buyThis = [];
        $avoidThis = [];

        for ($i = 0; $i < $this->countStylesToTake; $i++) {
            $styleToTake = $this->styleToTake[] = $this->includedIds[$i];
            $buyThis[] = DB::select("SELECT * FROM beers WHERE id = $styleToTake");
        }


        for ($i = 0; $i < $this->countStylesToAvoid; $i++) {
            $styleToAvoid = $this->styleToAvoid[] = $this->excludedIds[$i];
            $avoidThis[] = DB::select("SELECT * FROM beers WHERE id = $styleToAvoid");
        }

        //TODO
        try {
            $this->logStyles($name, $email, $newsletter);
        } catch (\Exception $e) {
            //mail('kontakt@piwolucja.pl', 'logStyles Exception', $e->getMessage());
        }

        $PKStyleTake = [];
        foreach ($this->styleToTake AS $index => $id) {
            if (PKAPI::getBeerInfo($id) !== null) {
                $PKStyleTake[] = PKAPI::getBeerInfo($id);
            } else {
                $PKStyleTake[] = '';
            }
        }

        return view('results', [
            'buyThis' => $buyThis,
            'avoidThis' => $avoidThis,
            'mustTake' => $this->mustTake,
            'mustavoid' => $this->mustAvoid,
            'PKStyleTake' => $PKStyleTake,
            'username' => $name,
            'barrelAged' => $this->barrelAged,
            'answers' => $this->answersDecoded
        ]);
    }

    /**
     * @param string $name
     * @param string $email
     * @param int $newsletter
     */
    private function logStyles(string $name, string $email, int $newsletter): void
    {

        $lastID = DB::select('SELECT MAX(id_answer) AS lastid FROM `styles_logs` LIMIT 1');
        $nextID = (int)$lastID[0]->lastid + 1;

        for ($i = 0; $i < 3; $i++) {
            DB::insert('INSERT INTO `styles_logs` 
                          (id_answer, 
                           username, 
                           email, 
                           newsletter, 
                           style_take, 
                           style_avoid, 
                           ip_address, 
                           created_at)
    					VALUES
    					(?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $nextID,
                    $name,
                    $email,
                    $newsletter,
                    $this->styleToTake[$i],
                    $this->styleToAvoid[$i],
                    $_SERVER['REMOTE_ADDR'],
                    now()
                ]);
        }
    }
}
