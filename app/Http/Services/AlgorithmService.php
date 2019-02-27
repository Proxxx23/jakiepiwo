<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\User;
use App\Http\Repositories\ScoringRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PolskiKraft\PolskiKraftService AS PKAPI;
use Illuminate\View\View;

class AlgorithmService
{
    /** @var array */
    protected $answersDecoded = [];

    //todo wywalić
    /** @var array */
    protected $styleToTake = []; // Styles user should buy
    /** @var array */
    protected $styleToAvoid = []; // Styles user should avoid

    /** @var ScoringRepositoryInterface */
    protected $scoringRepository;

    public function __construct( ScoringRepositoryInterface $scoringRepository )
    {
        $this->scoringRepository = $scoringRepository;
    }

    /**
     * Buduje siłę dla konkretnych ID stylu
     * Jeśli id ma postać 5:2.5 to zwiększy (przy trafieniu w to ID) punktację tego stylu o 2.5 a nie o 1
     * Domyślnie zwiększa punktację stylu o 1
     *
     * @param string $styleIds
     *
     * @return array|null
     */
    private function buildStrength( ?string $styleIds ): ?array
    {
        if ($styleIds === null) {
            return null;
        }

        $idsExploded = \explode( ',', trim( $styleIds ) );
        $idsToCalculate = [];

        foreach ( $idsExploded as $idMultiplierPair ) {
            if ( \strpos( $idMultiplierPair, ':' ) !== false || \strpos( $idMultiplierPair, ' :' ) !== false ) {
                $tmp = \explode( ':', $idMultiplierPair );
                $idsToCalculate[$tmp[0]] = (float)$tmp[1];
            } else {
                $idsToCalculate[$idMultiplierPair] = 1;
            }
        }

        return $idsToCalculate;
    }

    /**
     * Positive and negative synergies executor
     * There are all the synergies
     *
     * @param array $answerValue
     * @param User $user
     */
    public function applySynergy( array $answerValue, User $user ): void
    {
        // Lekkie + owocowe + Kwaśne
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[12] === 'tak' &&
            $answerValue[13] === 'chętnie' ) {
            echo 'Synergia Lekkie + owocowe + Kwaśne <br />';
            $user->options->buildPositiveSynergy( [ 40, 56 ], 2 );
            $user->options->buildPositiveSynergy( [ 51 ], 1.5 );
        }
        // nowe smaki LUB szokujące + złożone + jasne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'jasne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            echo 'Synergia we smaki LUB szokujące + złożone + jasne <br />';
            $user->options->buildPositiveSynergy( [ 7, 15, 16, 23, 39, 42, 50, 60, 73 ], 2 );
        }

        // nowe smaki LUB szokujące + złożone + ciemne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'ciemne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            echo 'Synergia nowe smaki LUB szokujące + złożone + ciemne <br />';
            $user->options->buildPositiveSynergy( [ 36, 37, 58, 59, 62, 63 ], 2 );
        }

        // złożone + ciemne + nieowocowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[12] === 'nie' ) {
            echo 'Synergia złożone + ciemne + nieowocowe <br />';
            $user->options->buildPositiveSynergy( [ 3, 24, 35, 36, 37, 48, 58, 59, 62, 63, 75 ], 1.5 );
        }

        // złożone + ciemne + nieowocowe + kawowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[10] === 'tak' &&
            $answerValue[12] === 'nie' ) {
            echo 'Synergia złożone + ciemne + nieowocowe + kawowe <br />';
            $user->options->buildPositiveSynergy( [ 74 ], 2.5 );
        }

        // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna)
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[7] === 'słodsze' &&
            !\in_array( $answerValue[5], [ 'mocną', 'jestem hopheadem' ] ) ) {
            echo 'Synergia Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna) <br />';
            $user->options->buildPositiveSynergy( [ 12, 29, 30, 34, 64 ], 2 );
            $user->options->buildNegativeSynergy( [ 36, 37 ], 3 );
        }

        // jasne + nieczekoladowe
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' ) {
            echo 'Synergia jasne + nieczekoladowe <br />';
            $user->options->buildNegativeSynergy(
                [ 12, 21, 24, 29, 33, 34, 35, 36, 37, 58, 59, 62, 63, 71, 74, 75 ], 2
            );
        }

        // ciemne + czekoladowe + lżejsze
        if ( $answerValue[6] === 'ciemne' &&
            $answerValue[9] === 'tak' &&
            $answerValue[8] !== 'mocne i gęste' ) {
            echo 'Synergia ciemne + czekoladowe + lżejsze <br />';
            $user->options->buildPositiveSynergy( [ 12, 31, 33, 34, 35, 59, 71 ], 2.5 );
        }


        // goryczka ledwo || lekka + jasne + nieczkoladowe + niegęste
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' &&
            $answerValue[8] !== 'mocne i gęste' &&
            ( $answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką' ) ) {
            echo 'Synergia goryczka ledwo || lekka + jasne + nieczkoladowe + niegęste <br />';
            $user->options->buildPositiveSynergy( [ 20, 25, 40, 44, 45, 47, 51, 52, 53, 68, 73 ], 2 );
            $user->options->buildNegativeSynergy( [ 3, 24, 35, 36, 37, 58, 59, 62, 63, 71, 75 ], 2 );
        }

        // jasne + lekkie + wodniste + wędzone = grodziskie
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'jasne' &&
            $answerValue[8] === 'wodniste' &&
            $answerValue[14] === 'tak' ) {
            echo 'Synergia asne + lekkie + wodniste + wędzone = grodziskie <br />';
            $user->options->buildPositiveSynergy( [ 52 ], 3 );
            $user->options->buildNegativeSynergy( [ 3, 22, 23, 24, 35, 36, 37, 50, 58, 59, 62, 63, 71, 75 ], 2 );
        }

        // duża/hophead goryczka + jasne
        if ( $answerValue[6] === 'jasne' &&
            ( $answerValue[5] === 'mocną' || $answerValue[5] === 'jestem hopheadem' ) ) {
            echo 'Synergia duża/hophead goryczka + jasne <br />';
            $user->options->buildPositiveSynergy( [ 1, 2, 5, 6, 7, 8, 28, 61 ], 1.75 );
            $user->options->buildPositiveSynergy( [ 65, 69, 70, 72 ], 1.5 );
        }

        // duża/hophead goryczka + ciemne
        if ( $answerValue[6] === 'ciemne' &&
            ( $answerValue[5] === 'mocną' || $answerValue[5] === 'jestem hopheadem' ) ) {
            echo 'Synergia duża/hophead goryczka + ciemne <br />';
            $user->options->buildPositiveSynergy( [ 3, 36, 37, 58, 62, 63, 75 ], 1.75 );
        }

        // goryczka ledwo || lekka
        if ( $answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką' ) {
            echo 'Synergia negatywna na lekkie goryczki <br />';
            $user->options->buildNegativeSynergy( [ 1, 2, 3, 5, 7, 8, 28, 61 ], 2 );
            $user->options->buildNegativeSynergy( [ 6, 60, 65, 69, 71, 72 ], 1.5 );
        }
    }

    /**
     * @param string $answers
     * @param User $user
     *
     * @return View
     * @throws \Exception
     */
    public function fetchProposedStyles( string $answers, User $user ): View
    {
        $answersDecoded = $this->answersDecoded = \json_decode( $answers );

        //todo: intersect?
        foreach ( $answersDecoded AS $questionNumber => &$givenAnswer ) {

            $scoringMap = $this->scoringRepository->fetchScore( $questionNumber );

            foreach ( $scoringMap AS $mappedAnswer => $ids ) {

                ($answersDecoded[14] === 'tak') ? $user->options->barrelAged = true : null;

                if ( $answersDecoded[12] === 'nie ma mowy' ) {
                    $user->options->excludeFromRecommended( [ 40, 42, 44, 51, 56 ] );
                }

                if ( $answersDecoded[13] === 'nie' ) {
                    $user->options->excludeFromRecommended( [ 15, 16, 52, 57, 58, 59, 62, 63 ] );
                }

                // Nie idź dalej przy BA
                // TODO: czemu?
                if ( \in_array( $ids, [ 'tak', 'nie' ], true ) ) {
                    continue;
                }

                if ( $givenAnswer === $mappedAnswer &&
                    $givenAnswer !== 'bez znaczenia' ) {
                    $idsToCalculate = $this->buildStrength( $ids );
                    if ($idsToCalculate !== null) {
                        foreach ( $idsToCalculate as $styleId => $strength ) {
                            if ( empty( $user->options->includedIds[$styleId] ) ) {
                                $user->options->addToIncluded( $styleId, $strength );
                            } else {
                                $user->options->addStrengthToIncluded( $styleId, $strength );
                            }
                        }
                    }
                }

                if ( $givenAnswer !== $mappedAnswer &&
                    $givenAnswer !== 'bez znaczenia' &&
                    !\in_array( $questionNumber, [ 3, 5, 9 ], true ) ) {
                    $idsToCalculate = $this->buildStrength( $ids );
                    if ($idsToCalculate !== null) {
                        foreach ( $idsToCalculate as $styleId => $strength ) {
                            if ( empty( $user->options->excludedIds[$styleId] ) ) {
                                $user->options->addToExcluded( $styleId, $strength );
                            } else {
                                $user->options->addStrengthToExcluded( $styleId, $strength );
                            }
                        }
                    }
                }

            }
        }
        unset( $givenAnswer );

        $this->applySynergy( $answersDecoded, $user );

        return $this->chooseStyles( $user );
    }

    /**
     * @param User $user
     *
     * @return View
     * @throws \Exception
     * todo: dodać mechanizm, który informuje, że granice były marginalne i wyniki mogą byc niejednoznaczne
     */
    public function chooseStyles( User $user ): View
    {
        $user->options->fetchAll();

        if ( $_SERVER['REMOTE_ADDR'] === '213.241.3.97' ) {
            echo '<br /><br /><br />';
            echo 'Tablica ze stylami do wybrania i punktami / Tablica ze stylami do odrzucenia i punktami: <br />';
            dd( $user->options->includedIds, $user->options->excludedIds );
            echo '<br />: <br />';
        }

        $user->options->removeAssignedPoints();

        //todo refactor (poza pętlę)
        $buyThis = [];
        for ( $i = 0; $i < $user->options->countStylesToTake; $i++ ) {
            $styleToTake = $this->styleToTake[] = $user->options->includedIds[$i]; // todo: do obiektu
            $buyThis[] = DB::select( "SELECT * FROM beers WHERE id = $styleToTake" );
        }

        //todo refactor (poza pętlę)
        $avoidThis = [];
        for ( $i = 0; $i < $user->options->countStylesToAvoid; $i++ ) {
            $styleToAvoid = $this->styleToAvoid[] = $user->options->excludedIds[$i]; // todo: do obiektu
            $avoidThis[] = DB::select( "SELECT * FROM beers WHERE id = $styleToAvoid" );
        }

        //TODO
        try {
            $this->logStyles( $user );
        } catch ( \Exception $e ) {
            //mail('kontakt@piwolucja.pl', 'logStyles Exception', $e->getMessage());
        }

        // todo: object ???
        $poliskiKraftStylesToTake = [];
        foreach ( $this->styleToTake as $beerId ) {
            $poliskiKraftStylesToTake[] = PKAPI::getBeerInfo( $beerId ) ?? null;
        }

        // TODO: Ideally with no array, just serializale object
        return view(
            'results', [
                'buyThis' => $buyThis,
                'avoidThis' => $avoidThis,
                'mustTake' => $user->options->mustTakeOpt,
                'mustAvoid' => $user->options->mustAvoidOpt,
                'PKStyleTake' => $poliskiKraftStylesToTake,
                'username' => $user->username,
                'barrelAged' => $user->options->barrelAged,
                'answers' => $this->answersDecoded,
            ]
        );
    }

    /**
     * @param User $user
     */
    private function logStyles( User $user ): void
    {
        $lastID = DB::select( 'SELECT MAX(id_answer) AS lastid FROM `styles_logs` LIMIT 1' );
        $nextID = (int)$lastID[0]->lastid + 1;

        $stylesCount = 3;

        //todo: jeden insert
        for ( $i = 0; $i < $stylesCount; $i++ ) {
            DB::insert(
                'INSERT INTO `styles_logs` 
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
                    $user->username,
                    $user->email,
                    $user->newsletterOpt,
                    $this->styleToTake[$i],
                    $this->styleToAvoid[$i],
                    $_SERVER['REMOTE_ADDR'],
                    now(),
                ]
            );
        }
    }
}
