<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\Answers;
use App\Http\Objects\PolskiKraftData;
use App\Http\Objects\PolskiKraftDataCollection;
use App\Http\Utils\Dictionary;
use App\Http\Utils\Filters;
use App\Http\Utils\UserCache;
use Carbon\Carbon;
use GuzzleHttp\ClientInterface;

//todo: right now this is a service - change to service
final class PolskiKraftRepository implements PolskiKraftRepositoryInterface
{
    // private const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';
    private const BEER_LIST_BY_STYLE_URL_PATTERN = 'https://www.polskikraft.pl/openapi/style/%d/examples';
    private const RAW_RESULTS_CACHE_KEY_SUFFIX = 'POLSKIKRAFT';
    private const USER_CACHE_KEY_SUFFIX = 'USER';
    private const LAST_UPDATED_DAYS_LIMIT = 60;
    private const LAST_UPDATED_MAX_DAYS = 180; // maximum limit if no beers found for last LAST_UPDATED_DAYS_LIMIT days
    private const BEERS_TO_SHOW_LIMIT = 3;
    private const MINIMAL_RATING = 3.0;

    private Answers $answers;
    private UserCache $cache;
    private Dictionary $dictionary;
    private ClientInterface $httpClient;

    public function __construct(
        Dictionary $dictionary,
        UserCache $cache,
        ClientInterface $httpClient
    ) {
        $this->cache = $cache;
        $this->dictionary = $dictionary;
        $this->httpClient = $httpClient;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function fetchByStyleId( string $density, int $styleId ): ?PolskiKraftDataCollection
    {
        if ( !\array_key_exists( $styleId, $this->dictionary->get() ) ) {
            return null;
        }

        $translatedStyleId = $this->dictionary->getById( $styleId );
        if ( $translatedStyleId === null ) {
            return null;
        }

        return \is_int( $translatedStyleId )
            ? $this->fetchOne( $styleId, $translatedStyleId, $density )
            : $this->fetchMultiple( $styleId, $translatedStyleId, $density );
    }

    /**
     * @param int $styleId
     * @param int $translatedStyleId
     *
     * @param string $density
     *
     * @return PolskiKraftDataCollection|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    private function fetchOne( int $styleId, int $translatedStyleId, string $density ): ?PolskiKraftDataCollection
    {
        $resultsCacheKey = $this->buildRawResultsCacheKey( $styleId );

        $cachedData = $this->cache->get( $resultsCacheKey );
        if ( $cachedData !== null ) {
            return $this->createPolskiKraftDataCollection( $cachedData, $styleId, $density );
        }

        $response = $this->httpClient->request(
            'GET',
            \sprintf( self::BEER_LIST_BY_STYLE_URL_PATTERN, $translatedStyleId )
        );

        if ( $response->getStatusCode() !== 200 ) {
            return null; //todo: any message or exception - this case should be covered
        }

        $data[$styleId] = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, \JSON_THROW_ON_ERROR
        );

        $this->cache->set( $resultsCacheKey, $data );

        if ( empty( $data ) ) {
            return null;
        }

        return $this->createPolskiKraftDataCollection( $data, $styleId, $density );
    }

    /**
     * @param int $styleId
     * @param array $translatedStyleIds
     *
     * @param string $density
     *
     * @return PolskiKraftDataCollection|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    private function fetchMultiple(
        int $styleId,
        array $translatedStyleIds,
        string $density
    ): ?PolskiKraftDataCollection {
        $resultsCacheKey = $this->buildRawResultsCacheKey( $styleId );

        $cachedData = $this->cache->get( $resultsCacheKey );
        if ( $cachedData !== null ) {
            return $this->createPolskiKraftDataCollection( $cachedData, $styleId, $density );
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

        $this->cache->set( $resultsCacheKey, $data );

        if ( $data === [] ) {
            return null;
        }

        return $this->createPolskiKraftDataCollection( $data, $styleId, $density );
    }

    private function buildRawResultsCacheKey( int $styleId ): string
    {
        return $styleId . '_' . self::RAW_RESULTS_CACHE_KEY_SUFFIX;
    }

    private function buildUserSpecificCacheKey( int $styleId ): string
    {
        $recommended = \implode( '_', $this->answers->getRecommendedIds() );
        $unsuitable = \implode( '_', $this->answers->getUnsuitableIds() );
        $hash  = \md5( $recommended . $unsuitable );

        return $styleId . '_' . $hash . '_' . self::USER_CACHE_KEY_SUFFIX . '_' . \time();
    }

    private function createPolskiKraftDataCollection( array $data, int $styleId, string $density ): PolskiKraftDataCollection
    {
        $beers = $this->retrieveBestBeers( $data, $density );

        $polskiKraftDataCollection = new PolskiKraftDataCollection();
        foreach ( $beers as $beer ) {
            $polskiKraftData = new PolskiKraftData( $beer );
            $polskiKraftDataCollection->add( $polskiKraftData->toArray() );
        }

        $userSpecificCacheKey = $this->buildUserSpecificCacheKey( $styleId );
        if ( $userSpecificCacheKey !== null ) {
            $this->cache->set( $userSpecificCacheKey, $polskiKraftDataCollection );
            $polskiKraftDataCollection->setCacheKey( $userSpecificCacheKey );
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

        $beersToShow = $beersNotToShow = [];
        foreach ( $beers as &$beer ) {
            $beerRating = (float) $beer['rating'];

            $daysToLastUpdated = $this->calculateDaysToLastUpdate( $beer['updated_at'] );
            if ( $this->isRatedInLastMonthsAndHasProperRating( $daysToLastUpdated, $beerRating ) ) {
                $beersToShow[] = $beer;
            } elseif ( $this->isRatedMaxHalfYearAgoAndHasProperRating( $daysToLastUpdated, $beerRating ) ) {
                $beersNotToShow[] = $beer;
            }

            if ( \count( $beersToShow ) === self::BEERS_TO_SHOW_LIMIT ) {
                return $beersToShow;
            }
        }
        unset( $beer );

        $beersToShowCount = \count( $beersToShow );

        if ( $beersToShowCount < self::BEERS_TO_SHOW_LIMIT ) {
            $remaining = self::BEERS_TO_SHOW_LIMIT - $beersToShowCount;
            $beersToAppend = \array_slice( $beersNotToShow, 0, $remaining );
            foreach ( $beersToAppend as $style ) {
                $beersToShow[] = $style;
            }
        }

        $this->sortByRating( $beersToShow );

        return $beersToShow;
    }

    private function isRatedInLastMonthsAndHasProperRating( int $daysToLastUpdated, float $beerRating ): bool
    {
        return $daysToLastUpdated < self::LAST_UPDATED_DAYS_LIMIT
            && $beerRating >= self::MINIMAL_RATING;
    }

    private function isRatedMaxHalfYearAgoAndHasProperRating( int $daysToLastUpdated, float $beerRating ): bool
    {
        return $daysToLastUpdated > self::LAST_UPDATED_DAYS_LIMIT
            && $daysToLastUpdated < self::LAST_UPDATED_MAX_DAYS && $beerRating >= self::MINIMAL_RATING;
    }

    private function calculateDaysToLastUpdate( int $updatedAt ): int
    {
        return Carbon::now()
            ->diffInDays( Carbon::createFromTimestamp( $updatedAt ) );
    }

    private function sortByRating( array &$beers ): void
    {
        \usort(
            $beers, static function ( array $a, array $b ) {
            return ( $b['rating'] <=> $a['rating'] );
        }
        );
    }
}
