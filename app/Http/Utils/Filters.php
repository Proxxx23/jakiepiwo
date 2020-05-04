<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Http\Objects\Answers;

final class Filters
{
    private const EXCLUDE_FILTERS = [
        'smoked' => [ 'wędz', 'smoke', 'wedz', 'dym', 'szynk', 'torf', 'islay', 'laphroaig', 'ardbeg' ],
        'sour' => [ 'kwaś', 'kwas', ],
        'coffee' => [ 'kawa', 'kawow', 'coffee', 'cafe', 'espresso', 'latte', 'cappucino', 'kawą', ],
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
        ],
    ];

    private const SPECIAL_BEER_STYLE_IDS = [73, 74,];
    private const SPECIAL_BEERS_FILTERS = [
        'milkshake' => ['shake', 'milk', 'szejk', 'laktoz', 'lactose'],
        'coffeestout' => ['coffee', 'beans', 'mocha', 'espresso', 'kaw', 'cafe', 'caffe', 'speciality'],
    ];

    public static function filter( Answers $answers, array &$beers, string $density ): void
    {
        $styleId = \array_key_first( $beers );
        if ( \in_array( $styleId, self::SPECIAL_BEER_STYLE_IDS, true ) ) {
            self::filterSpecialBeers( $answers, $beers, $density );
            return;
        }

        self::filterExclusions( $answers, $beers );
        self::filterImperials( $beers, $density );

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
        $patterns = self::getPregMatchExclusionsPatterns( $answers );
        if ( $patterns === null ) {
            return;
        }

        $beers = \reset( $beers );

        foreach ( $beers as $index => &$beer ) {
            $beerName = $beer['title'];
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            foreach ( $patterns as $pattern ) {
                if ( \preg_match( $pattern, $beerName ) ||
                    \preg_match( $pattern, \implode( ',', $beerKeywords) ) ) {
                    unset( $beers[$index] );
                }
            }
        }
        unset( $beer );
    }

    private static function filterImperials( array &$beers, string $density ): void
    {
        if ( $density !== 'wodniste i lekkie' ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = $beer['title'];
            $beerSubtitle = $beer['subtitle_alt'];
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            if ( \preg_match( '/.*imperial.*/i', $beerName ) ||
                \preg_match( '/.*imperial.*/i', $beerSubtitle ) ||
                \preg_match( '/.*imperial.*/i', \implode( ',', $beerKeywords) ) ) {
                unset( $beers[$index] );
            }
        }
        unset( $beer );
    }

    /**
     * Milkshake IPA & Coffee Stout has no style in PolskiKraft
     * We must filter those using regular styles and keywords combination
     *
     * @param Answers $answers
     * @param array $beers
     * @param string $density
     */
    private static function filterSpecialBeers( Answers $answers, array &$beers, string $density ): void
    {
        $styleId = \array_key_first( $beers );

        $specialPatterns = self::getPregMatchSpecialBeersPatterns( \array_flip( $answers->getRecommendedIds() ) );
        if ( $specialPatterns === null ) {
            return;
        }

        $beers = \reset( $beers );

        foreach ( $beers as $index => &$beer ) {
            $beerName = $beer['title'];
            $beerSubtitle = $beer['subtitle_alt'];
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );

            foreach ( $specialPatterns as $pattern ) {
                if ( $styleId === 73 && //milkshake
                    !\preg_match( $pattern, $beerName ) &&
                    !\preg_match( $pattern, $beerSubtitle ) &&
                    !\preg_match( $pattern, \implode(',', $beerKeywords ) ) ) {
                    unset( $beers[$index] );
                } elseif ( $styleId === 74 && //coffee stout
                    !\preg_match( $pattern, $beerName ) &&
                    !\preg_match( $pattern, $beerSubtitle ) ) {
                    unset( $beers[$index] );
                }
            }
        }
        unset( $beer );

        $excludePatterns = self::getPregMatchExclusionsPatterns( $answers );
        if ( $excludePatterns === null ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = $beer['title'];
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            foreach ( $excludePatterns as $pattern ) {
                if ( \preg_match( $pattern, $beerName ) ||
                    \preg_match( $pattern, \implode( ',', $beerKeywords) ) ) {
                    unset( $beers[$index] );
                }
            }
        }
        unset( $beer );

        self::filterImperials( $beers, $density );
    }

    private static function getPregMatchExclusionsPatterns( Answers $answers ): ?array
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

        $patterns = null;
        foreach ( $filters as $filter ) {
            $patterns[] = '/.*' . \implode( '|', self::EXCLUDE_FILTERS[$filter] ) . '.*/i';
        }

        return $patterns;
    }

    private static function getPregMatchSpecialBeersPatterns( array $includedIds ): ?array
    {
        if ( !\array_key_exists( 73, $includedIds ) && !\array_key_exists( 74, $includedIds ) ) {
            return null;
        }

        $filters = null;
        if ( \array_key_exists( 73, $includedIds ) ) {
            $filters[] = 'milkshake';
        }

        if ( \array_key_exists( 74, $includedIds ) ) {
            $filters[] = 'coffeestout';
        }

        if ( $filters === null ) {
            return null;
        }

        $patterns = null;
        foreach ( $filters as $filter ) {
            $patterns[] = '/.*' . \implode( '|', self::SPECIAL_BEERS_FILTERS[$filter] ) . '.*/i';
        }

        return $patterns;
    }
}
