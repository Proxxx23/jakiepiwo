<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\Answers;
use App\Http\Objects\PolskiKraftData;
use App\Http\Objects\PolskiKraftDataCollection;
use App\Http\Utils\Dictionary;
use App\Http\Utils\Filters;
use App\Http\Utils\SharedCache;
use Carbon\Carbon;
use GuzzleHttp\ClientInterface;

//todo: right now this is a service - change to service and move fetch to repo
final class PolskiKraftRepository implements PolskiKraftRepositoryInterface
{
    private const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';
    private const BEER_LIST_BY_STYLE_URL_PATTERN = 'https://www.polskikraft.pl/openapi/style/%d/examples';
    private const RAW_RESULTS_CACHE_KEY_SUFFIX = 'POLSKIKRAFT';
    private const LAST_UPDATED_DAYS_LIMIT = 3;
    private const CREATION_DAYS_LIMIT = 14;
    private const LAST_UPDATED_DAYS_LIMIT_SECOND_TURN = 7;
    private const LAST_UPDATED_MAX_DAYS = 180; // maximum limit if no beers found for last LAST_UPDATED_DAYS_LIMIT days
    private const BEERS_TO_SHOW_LIMIT = 3;
    private const MINIMAL_RATING = 2.5;

    private Answers $answers;
    private bool $connectionError;
    private SharedCache $cache;
    private Dictionary $dictionary;
    private ClientInterface $httpClient;
    private UntappdRepositoryInterface $untappdRepository;

    public function __construct(
        Dictionary $dictionary,
        SharedCache $cache,
        ClientInterface $httpClient,
        UntappdRepositoryInterface $untappdRepository
    ) {
        $this->httpClient = $httpClient;
        $this->connectionError = $this->checkIsConnectionRefused();
        if ( $this->checkIsConnectionRefused() ) {
            return; // we don't want to go further if connection refused
        }
        $this->cache = $cache;
        $this->dictionary = $dictionary;
        $this->untappdRepository = $untappdRepository;
    }

    public function connectionRefused(): bool
    {
        return $this->connectionError;
    }

    public function setUserAnswers( Answers $answers ): self
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * @param string $density
     * @param int $styleId
     *
     * @return PolskiKraftDataCollection|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    public function fetchByStyleId( string $density, int $styleId ): ?PolskiKraftDataCollection
    {
        $translatedStyleIds = $this->dictionary->getById( $styleId );
        if ( $translatedStyleIds === null ) {
            return null;
        }

        $resultsCacheKey = $styleId . '_' . self::RAW_RESULTS_CACHE_KEY_SUFFIX;

        $cachedData = $this->cache->get( $resultsCacheKey );
        if ( $cachedData !== null ) {
            return $this->createPolskiKraftDataCollection( $cachedData, $density );
        }

        $data = [];
        foreach ( $translatedStyleIds as $translatedId ) {
            $response = $this->httpClient->request(
                'GET',
                \sprintf( self::BEER_LIST_BY_STYLE_URL_PATTERN, $translatedId )
            );

            $results = \json_decode(
                $response->getBody()
                    ->getContents(), true, 512, \JSON_THROW_ON_ERROR
            );

            if ( empty( $results ) ) {
                continue;
            }

            foreach ( $results as $result ) {
                $data[$styleId][] = $result;
            }
        }

//        $this->untappdRepository->add( $data[$styleId] );

        $this->cache->set( $resultsCacheKey, $data );

        if ( $data === [] ) {
            return null;
        }

        return $this->createPolskiKraftDataCollection( $data, $density );
    }

    private function createPolskiKraftDataCollection( array $data, string $density ): PolskiKraftDataCollection
    {
        $bestBeers = $this->retrieveBestBeers( $data, $density );

        $polskiKraftDataCollection = new PolskiKraftDataCollection();
        foreach ( $bestBeers as $beer ) {
            $polskiKraftData = new PolskiKraftData( $beer );
            $polskiKraftDataCollection->add( $polskiKraftData->toArray() );
        }

        return $polskiKraftDataCollection;
    }

    /**
     * It takes 3 best scored beers from last LAST_UPDATED_DAYS_LIMIT days (updated_at)
     * If not found, try to append older beers to an array that returns beer
     * Max beer age (updated_at) is LAST_UPDATED_MAX_DAYS
     *
     * Example:
     * - we have 1 out of 3 slots occupied by beers < LAST_UPDATED_DAYS_LIMIT days old
     * - we have 7 beers that are older
     * - we take first 2 beers and add to first 3, having 3 of 3 slots full
     *
     * @param array $beers
     * @param string $density
     *
     * @return array
     */
    private function retrieveBestBeers( array $beers, string $density ): array
    {
        Filters::filter( $this->answers, $beers, $density );
        $this->sortByRating( $beers );

        $beersToShow = $beersLeft = $beersToShowSecondTurn = [];
        foreach ( $beers as &$beer ) {
            $beerRating = (float) $beer['rating'];

            $daysToLastUpdated = $this->calculateDaysTo( $beer['updated_at'] );
            $daysToCreation = $this->calculateDaysTo( $beer['created_at'] );
            if ( $this->isJustRatedOrCreatedAndHasProperRating( $daysToCreation, $daysToLastUpdated, $beerRating ) ) {
                $beersToShow[] = $beer;
            } elseif ( $this->isRatedInLastWeeksAndHasProperRating( $daysToLastUpdated, $beerRating ) ) {
                $beersToShowSecondTurn[] = $beer;
            } elseif ( $this->isRatedMaxHalfYearAgoAndHasProperRating( $daysToLastUpdated, $beerRating ) ) {
                $beersLeft[] = $beer;
            }

            if ( \count( $beersToShow ) === self::BEERS_TO_SHOW_LIMIT ) {
                return $beersToShow;
            }
        }
        unset( $beer );

        $beersToShowCount = \count( $beersToShow );
        $remaining = self::BEERS_TO_SHOW_LIMIT - $beersToShowCount;
        if ( $beersToShowCount < self::BEERS_TO_SHOW_LIMIT && $remaining > 0 ) {
            // check first turn (up to 7 days) and append
            $beersToAppend = \array_slice( $beersToShowSecondTurn, 0, $remaining );
            foreach ( $beersToAppend as $style ) {
                $beersToShow[] = $style;
                if ( \count( $beersToShow ) >= self::BEERS_TO_SHOW_LIMIT ) {
                    break;
                }
            }
        }


        $beersToShowCount = \count( $beersToShow );
        $remaining = self::BEERS_TO_SHOW_LIMIT - $beersToShowCount;
        if ( $beersToShowCount < self::BEERS_TO_SHOW_LIMIT && $remaining > 1 ) {
            $beersToAppend = \array_slice( $beersLeft, 0, $remaining );
            foreach ( $beersToAppend as $style ) {
                $beersToShow[] = $style;
                if ( \count( $beersToShow ) >= self::BEERS_TO_SHOW_LIMIT ) {
                    break;
                }
            }
        }

        $this->sortByRating( $beersToShow );

        return $beersToShow;
    }

    private function isJustRatedOrCreatedAndHasProperRating( int $daysToCreation, int $daysToLastUpdated, float $beerRating ): bool
    {
        return ( $daysToLastUpdated < self::LAST_UPDATED_DAYS_LIMIT &&
            $beerRating >= self::MINIMAL_RATING ) || $daysToCreation < self::CREATION_DAYS_LIMIT;
    }

    private function isRatedInLastWeeksAndHasProperRating( int $daysToLastUpdated, float $beerRating ): bool
    {
        return $daysToLastUpdated < self::LAST_UPDATED_DAYS_LIMIT_SECOND_TURN &&
            $beerRating >= self::MINIMAL_RATING;
    }

    private function isRatedMaxHalfYearAgoAndHasProperRating( int $daysToLastUpdated, float $beerRating ): bool
    {
        return $daysToLastUpdated > self::LAST_UPDATED_DAYS_LIMIT &&
            $daysToLastUpdated < self::LAST_UPDATED_MAX_DAYS &&
            $beerRating >= self::MINIMAL_RATING;
    }

    private function calculateDaysTo( int $timestamp ): int
    {
        return Carbon::now()
            ->diffInDays( Carbon::createFromTimestamp( $timestamp ) );
    }

    private function sortByRating( array &$beers ): void
    {
        \usort(
            $beers, static function ( array $a, array $b ) {
            return ( $b['rating'] <=> $a['rating'] );
        }
        );
    }

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function checkIsConnectionRefused(): bool
    {
        $response = $this->httpClient->request(
            'GET', self::DEFAULT_LIST_URI, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        return empty(
            $response->getBody()
                ->getContents()
            ) || $response->getStatusCode() !== 200;
    }
}
