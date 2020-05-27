<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Helpers\Helper;
use App\Http\Objects\Answers;

final class Filters
{
    private const EXCLUDE_FILTERS = [
        'smoked' => [
            'wędz',
            'smoke',
            'wedz',
            'dym',
            'szynk',
            'torf',
            'islay',
            'laphroaig',
            'ardbeg',
            'caol ila',
            'bowmore',
            'bunnahabhain',
            'bruichladdich',
            'kilchoman',
            'lagavulin',
        ],
        'sour' => [ 'kwaś', 'kwas', 'sour', 'lambic', 'gueuze', 'tart', ],
        'coffee' => [
            'kawa',
            'kawow',
            'coffee',
            'cafe',
            'espresso',
            'latte',
            'cappucino',
            'kawą',
            'arabica',
            'robusta',
        ],
        'chocolate' => [ 'choco', 'cacao', 'cocoa', 'kakao', 'czekolad', ],
        'barrelaged' => [
            'barrel-aged',
            'double BA',
            'triple BA',
            'B\.A\.',
            'barrel aged',
            'whisky',
            'bourbon',
            'islay',
            'slyrs',
            'burbon',
            'rum',
            'jack daniels',
            'jd',
            'whiskey',
            'laphroaig',
            'ardbeg',
            'cognac',
            'brandy',
            'woodford',
            'islay',
            'ardbeg',
            'caol ila',
            'bowmore',
            'bunnahabhain',
            'bruichladdich',
            'kilchoman',
            'lagavulin',
            'eagle rare',
        ],
    ];

    private const SPECIAL_BEER_STYLE_IDS = [ 22, 57, 73, 74, 998, 999, ];
    private const SPECIAL_BEERS_FILTERS = [
        'smokedale' => [ 'smoke', 'dym', 'wędz', 'rauch', 'islay', 'szynk', 'boczek', 'boczk', 'kiełbas', ],
        'milkshake' => [ 'szejk', 'milk', 'shake', ],
        'coffeestout' => [
            'coffee',
            'mocha',
            'espresso',
            'kaw',
            'cafe',
            'caffe',
            'robusta',
            'arabica',
            'latte',
            'cappucino',
        ],
        'pastry' => [ 'pastry', ],
        'doppelbock' => [ 'doppel', 'ice', 'eis', 'double' ],
    ];

    private const IMPERIAL_STYLE_IDS = [ 7, 36, 37, ];
    private const IMPERIAL_BEERS_PATTERN = '/.*imperial|ice|double|triple|quad[^r]|wymraz|wymraż|imperium|doppel|eis[^s].*/i';

    public static function filter( Answers $answers, array &$beers, string $density ): void
    {
        $styleId = \array_key_first( $beers );
        $beers = \reset( $beers );
        if ( \in_array( $styleId, self::SPECIAL_BEER_STYLE_IDS, true ) ) {
            self::filterSpecialBeers( $answers, $styleId, $beers, $density );
            return;
        }

        self::filterExclusions( $answers, $beers );
        self::filterImperials( $beers, $styleId, $density );
    }

    /**
     * Filters beers basing on answers, for example removes all beers with smoked tags from beer list
     *
     * @param Answers $answers
     * @param array $beers
     *
     * @return void
     */
    private static function filterExclusions( Answers $answers, array &$beers ): void
    {
        $exclusionsPattern = self::getPregMatchExclusionsPatterns( $answers );
        if ( $exclusionsPattern === null ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = \preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $beer['title'] );
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            if ( Helper::pregMatchMultiple( $exclusionsPattern, [ $beerName, \implode( ',', $beerKeywords ) ] ) ) {
                unset( $beers[$index] );
            }
        }
        unset( $beer );
    }

    /**
     * Remove imperial beers if someone don't want to have these beers
     *
     * @param array $beers
     * @param int $styleId
     * @param string $density
     */
    private static function filterImperials( array &$beers, int $styleId, string $density ): void
    {
        if ( $density === 'coś ciężkiego' ) {
            return;
        }

        if ( \in_array( $styleId, self::IMPERIAL_STYLE_IDS, true ) ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = \preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $beer['title'] );
            $beerSubtitle = \preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $beer['subtitle_alt'] );
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            if ( Helper::pregMatchMultiple(
                self::IMPERIAL_BEERS_PATTERN, [ $beerName, $beerSubtitle, \implode( ',', $beerKeywords ) ]
            ) ) {
                unset( $beers[$index] );
            }
        }
        unset( $beer );
    }

    /**
     * Milkshake IPA, Coffee Stout and Smoked Ales has no style in PolskiKraft
     * We must filter those using regular styles and keywords combination
     *
     * @param Answers $answers
     * @param int $styleId
     * @param array $beers
     * @param string $density
     */
    private static function filterSpecialBeers( Answers $answers, int $styleId, array &$beers, string $density ): void
    {
        if ( !\in_array( $styleId, self::SPECIAL_BEER_STYLE_IDS, true ) ) {
            return;
        }

        $specialPattern = self::getPregMatchSpecialBeersPatterns( $styleId );
        if ( $specialPattern === null ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = \preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $beer['title'] );
            $beerSubtitle = \preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $beer['subtitle_alt'] );
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            if ( !Helper::pregMatchMultiple(
                $specialPattern, [ $beerName, $beerSubtitle, \implode( ',', $beerKeywords ), ]
            ) ) {
                unset( $beers[$index] );
            }
        }
        unset( $beer );

        $exclusionsPattern = self::getPregMatchExclusionsPatterns( $answers );
        if ( $exclusionsPattern === null ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = \preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $beer['title'] );
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            if ( Helper::pregMatchMultiple( $exclusionsPattern, [ $beerName, \implode( ',', $beerKeywords ) ] ) ) {
                unset( $beers[$index] );
            }
        }
        unset( $beer );

        self::filterImperials( $beers, $styleId, $density );
    }

    private static function getPregMatchExclusionsPatterns( Answers $answers ): ?string
    {
        $filters = null;

        if ( !$answers->isSmoked() ) {
            $filters[] = 'smoked';
        }

        if ( !$answers->isChocolate() ) {
            $filters[] = 'chocolate';
        }

        if ( !$answers->isCoffee() ) {
            $filters[] = 'coffee';
        }

        if ( !$answers->isSour() ) {
            $filters[] = 'sour';
        }

        if ( !$answers->isBarrelAged() ) {
            $filters[] = 'barrelaged';
        }

        if ( $filters === null ) {
            return null;
        }

        $pattern = '/.*';
        foreach ( $filters as $filter ) {
            $pattern .= \implode( '|', self::EXCLUDE_FILTERS[$filter] ) . '|';
        }
        $pattern = \rtrim( $pattern, '|' );
        $pattern .= '.*/i';

        return $pattern;
    }

    private static function getPregMatchSpecialBeersPatterns( int $styleId ): ?string
    {
        switch ( $styleId ) {
            case 22:
                return '/.*' . \implode( '|', self::SPECIAL_BEERS_FILTERS['doppelbock'] ) . '.*/i';
            case 57:
                return '/.*' . \implode( '|', self::SPECIAL_BEERS_FILTERS['smokedale'] ) . '.*/i';
            case 73:
                return '/.*' . \implode( '|', self::SPECIAL_BEERS_FILTERS['milkshake'] ) . '.*/i';
            case 74:
                return '/.*' . \implode( '|', self::SPECIAL_BEERS_FILTERS['coffeestout'] ) . '.*/i';
            case 998:
            case 999:
                return '/.*' . \implode( '|', self::SPECIAL_BEERS_FILTERS['pastry'] ) . '.*/i';
            default:
                return null;
        }
    }
}
