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

//todo: right now this is a service - change to service
final class PolskiKraftRepository implements PolskiKraftRepositoryInterface
{
    // private const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';
    private const BEER_LIST_BY_STYLE_URL_PATTERN = 'https://www.polskikraft.pl/openapi/style/%d/examples';
    private const CACHE_KEY_SIMPLE_PATTERN = '%s_POLSKIKRAFT';
    private const CACHE_KEY_MULTIPLE_PATTERN = '%s_%s_POLSKIKRAFT';
    private const LAST_UPDATED_DAYS_LIMIT = 60;
    private const LAST_UPDATED_MAX_DAYS = 180; // maximum limit if no beers found for last LAST_UPDATED_DAYS_LIMIT days
    private const BEERS_TO_SHOW_LIMIT = 3;
    private const MINIMAL_RATING = 3.0;

    private Answers $answers;
    private SharedCache $cache;
    private Filters $filters;
    private Dictionary $dictionary;
    private ClientInterface $httpClient;

    public function __construct(
        Dictionary $dictionary,
        SharedCache $cache,
        Filters $filters,
        ClientInterface $httpClient
    ) {
        $this->cache = $cache;
        $this->dictionary = $dictionary;
        $this->filters = $filters;
        $this->httpClient = $httpClient;
    }

    public function setUserAnswers( Answers $answers ): self
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * @param int $styleId
     *
     * @return PolskiKraftDataCollection|null
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchByStyleId( int $styleId ): ?PolskiKraftDataCollection
    {
        if ( !\array_key_exists( $styleId, $this->dictionary->get() ) ) {
            return null;
        }

        $translatedStyleId = $this->dictionary->getById( $styleId );
        if ( $translatedStyleId === null ) {
            return null;
        }

        return \is_int( $translatedStyleId )
            ? $this->fetchOne( $translatedStyleId )
            : $this->fetchMultiple( $translatedStyleId );
    }

    /**
     * @param int $translatedStyleId
     *
     * @return PolskiKraftDataCollection|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    private function fetchOne( int $translatedStyleId ): ?PolskiKraftDataCollection
    {
        $cacheKey = \sprintf( self::CACHE_KEY_SIMPLE_PATTERN, $translatedStyleId );

        $cachedData = $this->cache->get( $cacheKey );
        if ( $cachedData !== null ) {
            $cachedData->setCacheKey( $cacheKey );

            return $cachedData;
        }

        $response = $this->httpClient->request(
            'GET',
            \sprintf( self::BEER_LIST_BY_STYLE_URL_PATTERN, $translatedStyleId )
        );

        if ( $response->getStatusCode() !== 200 ) {
            return null; //todo: any message or exception - this case should be covered
        }

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );

        if ( empty( $data ) ) {
            return null;
        }

        return $this->createPolskiKraftDataCollection( $data, $cacheKey );
    }

    /**
     * @param array $translatedStyleIds
     *
     * @return PolskiKraftDataCollection|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    private function fetchMultiple( array $translatedStyleIds ): ?PolskiKraftDataCollection
    {
        [ $firstId, $secondId ] = $translatedStyleIds;
        $cacheKey = \sprintf( self::CACHE_KEY_MULTIPLE_PATTERN, $firstId, $secondId );

        $cachedData = $this->cache->get( $cacheKey );
        if ( $cachedData !== null ) {
            $cachedData->setCacheKey( $cacheKey );

            return $cachedData;
        }

        $data = [];
        foreach ( $translatedStyleIds as $styleId ) {
            $response = $this->httpClient->request(
                'GET',
                \sprintf( self::BEER_LIST_BY_STYLE_URL_PATTERN, $styleId )
            );

            $results = \json_decode(
                $response->getBody()
                    ->getContents(), true, 512, JSON_THROW_ON_ERROR
            );

            $data[] = $results;
        }

        if ( $data[0] === [] ) {
            return null;
        }

        return $this->createPolskiKraftDataCollection( $data[0], $cacheKey );
    }

    private function createPolskiKraftDataCollection( array $data, string $cacheKey ): PolskiKraftDataCollection
    {
        $beers = $this->retrieveBestBeers( $data );

        $polskiKraftDataCollection = new PolskiKraftDataCollection();
        foreach ( $beers as $beer ) {
            $polskiKraftData = new PolskiKraftData( $beer );
            $polskiKraftDataCollection->add( $polskiKraftData->toArray() );
        }

        if ( $cacheKey !== null ) {
            $this->cache->set( $cacheKey, $polskiKraftDataCollection );
            $polskiKraftDataCollection->setCacheKey( $cacheKey );
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
     *
     * @return array
     */
    private function retrieveBestBeers( array $beers ): array
    {
        $this->filters->filter( $this->answers, $beers );
        $this->sortByRating( $beers );

        $beersToShow = $beersNotToShow = [];
        foreach ( $beers as $beer ) {
            $beerRating = (float) $beer['rating'];

            $daysToLastUpdated = $this->getDaysToLastUpdate( $beer['updated_at'] );

            if ( $this->isRatedInLastMonthsAndHasProperRating( $daysToLastUpdated, $beerRating ) ) {
                $beersToShow[] = $beer;
            } elseif ( $this->isRatedMaxHalfYearAgoAndHasProperRating( $daysToLastUpdated, $beerRating ) ) {
                $beersNotToShow[] = $beer;
            }

            if ( \count( $beersToShow ) === self::BEERS_TO_SHOW_LIMIT ) {
                return $beersToShow;
            }
        }

        $beersToShowCount = \count( $beersToShow );

        if ( $beersToShowCount < self::BEERS_TO_SHOW_LIMIT && \count( $beers ) >= 3 ) {
            $remaining = self::BEERS_TO_SHOW_LIMIT - $beersToShowCount;
            $beersToAppend = \array_slice( $beersNotToShow, 0, $remaining );
            foreach ( $beersToAppend as $beer ) {
                $beersToShow[] = $beer;
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

    private function getDaysToLastUpdate( int $updatedAt ): int
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
