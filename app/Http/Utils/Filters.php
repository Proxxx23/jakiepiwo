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

    private const SPECIAL_BEERS_FILTERS = [
        'milkshake' => ['shake', 'milk', 'szejk', 'laktoz', 'lactose'],
        'coffeestout' => ['coffee', 'beans', 'mocha', 'espresso', 'kaw', 'cafe', 'caffe', 'speciality'],
    ];

    public static function filter( Answers $answers, array &$beers ): void
    {
        self::filterSpecialBeers( $answers, $beers );
        self::filterExclusions( $answers, $beers );
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

        foreach ( $beers as $index => &$beer ) {
            $beerName = $beer['title'];
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            foreach ( $patterns as $pattern ) {
                if ( \preg_match( $pattern, $beerName ) ||
                    \preg_match( $pattern, \implode( ',', $beerKeywords ) ) ) {
                    unset( $beers[$index] );
                }
            }
        }
        unset( $beer );

        $beers = \array_values( $beers ); //reindex
    }

    /**
     * Milkshake IPA & Coffee Stout has no style in PolskIKraft
     * We must filter those using regular styles and keywords combination
     *
     * @param Answers $answers
     * @param array $beers
     */
    private static function filterSpecialBeers( Answers $answers, array &$beers ): void
    {
        $patterns = self::getPregMatchSpecialBeersPatterns( \array_flip( $answers->getIncludedIds() ) );
        if ( $patterns === null ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = $beer['title'];
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            foreach ( $patterns as $pattern ) {
                if ( !\preg_match( $pattern, $beerName ) &&
                    !\preg_match( $pattern, \implode( ',', $beerKeywords ) ) ) {
                    unset( $beers[$index] );
                }
            }
        }
        unset( $beer );

        $beers = \array_values( $beers ); //reindex
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
