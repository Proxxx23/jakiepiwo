<?php

namespace App\Http\Utils;

use App\Http\Objects\Answers;

final class Filters
{
    private const FILTER = [
        'smoked' => ['wędz', 'smoke', 'wedz', 'dym', 'szynk', 'torf', 'islay', 'laphroaig', 'ardbeg'],
        'sour' => ['kwaś', 'kwas',],
        'coffee' => ['kawa', 'kawow', 'coffee', 'cafe', 'espresso', 'latte', 'cappucino', 'kawą',],
        'chocolate' => ['choco', 'cacao', 'cocoa', 'kakao', 'czekolad',],
        'barrelaged' => ['barrel-aged', 'barrel aged', 'whisky', 'bourbon', 'burbon', 'rum', 'jack daniels', 'jd', 'whiskey'],
    ];

    /**
     * Filters beers basing on answers, for example removes all beers with smoked tags from beer list
     * @param Answers $answers
     * @param array $beers
     *
     * @return void
     */
    public function filter( Answers $answers, array &$beers ): void
    {
        $patterns = $this->getPregMatchPatterns( $answers );
        if ( $patterns === null ) {
            return;
        }

        foreach ( $beers as $index => &$beer ) {
            $beerName = $beer['title'];
            $beerKeywords = \array_column( $beer['keywords'], 'keyword' );
            foreach ( $patterns as $pattern ) {
                if ( \preg_match( $pattern, $beerName ) ||
                    \preg_match( $pattern, \implode(',', $beerKeywords ) ) ) {
                    unset( $beers[$index] );
                }
            }
        }
    }

    private function getPregMatchPatterns( Answers $answers ): ?array
    {
        $filters = null;
        if ( $answers->isSmoked( )) {
            $filters[] = 'smoked';
        }

        if ( $answers->isChocolate() ) {
            $filters[] = 'chocolate';
        }

        if ( $answers->isCoffee() ) {
            $filters[] = 'coffee';
        }

        if ( $answers->isSour() ) {
            $filters[] = 'sour';
        }

        if ( $answers->isBarrelAged() ) {
            $filters[] = 'barrelaged';
        }

        if ( $filters === null ) {
            return null;
        }

        $patterns = null;
        foreach ( $filters as $filter ) {
            $patterns[] = '/.*' . implode( '|', self::FILTER[$filter] ) . '.*/';
        }

        return $patterns;
    }
}
