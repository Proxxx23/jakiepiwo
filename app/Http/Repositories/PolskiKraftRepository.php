<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\Answers;
use App\Http\Objects\PolskiKraftData;
use App\Http\Objects\PolskiKraftDataCollection;
use App\Http\Utils\Dictionary;
use Carbon\Carbon;
use GuzzleHttp\ClientInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

//todo: right now this is a service - change to service
final class PolskiKraftRepository implements PolskiKraftRepositoryInterface
{
    // private const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';
    private const BEER_LIST_BY_STYLE_URL_PATTERN = 'https://www.polskikraft.pl/openapi/style/%d/examples';
    private const CACHE_KEY_SIMPLE_PATTERN = '%s_POLSKIKRAFT';
    private const CACHE_KEY_MULTIPLE_PATTERN = '%s_%s_POLSKIKRAFT';
    private const LAST_UPDATED_DAYS_LIMIT = 60;
    private const LAST_UPDATED_MAX_DAYS = 180; // maximum limit if no beers found for last LAST_UPDATED_DAYS_LIMIT days
    private const BEERS_COUNT_TO_SHOW = 3;

    private const FILTER = [
        'smoked' => ['wędz', 'smoke', 'wedz', 'dym', 'szynk', 'torf', 'islay'],
        'sour' => ['kwaś', 'kwas',],
        'coffee' => ['kawa', 'kawow', 'coffee', 'cafe', 'espresso', 'latte', 'cappucino', 'kawą'],
        'chocolate' => ['choco', 'cacao', 'cocoa', 'kakao', 'czekolad',],
    ];

    private Answers $answers;
    private FilesystemAdapter $cache;
    private Dictionary $dictionary;
    private ClientInterface $httpClient;

    public function __construct(
        Dictionary $dictionary,
        FilesystemAdapter $cache,
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

        $cachedData = $this->getFromCache( $cacheKey );
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

        return $this->createPolskiKraftCollection( $data, $cacheKey );
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

        $cachedData = $this->getFromCache( $cacheKey );
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

            foreach ( $results as $result ) {
                $data[] = $result; // meh... -.-
            }

        }

        if ( $data === [] ) {
            return null;
        }

        return $this->createPolskiKraftCollection( $data, $cacheKey );
    }

    /**
     * @param string $cacheKey
     *
     * @return mixed|null
     *
     * todo: ale to jest kurwa złe, wynieść to w pizdu SRP
     */
    private function getFromCache( ?string $cacheKey )
    {
        if ( $cacheKey === null ) {
            return null;
        }

        $item = null;
        try {
            $item = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $e ) {

        }

        return $item !== null && $item->isHit()
            ? $item->get()
            : null;
    }

    /**
     * todo: ale to jest kurwa złe, wynieść to w pizdu SRP
     *
     * @param string $cacheKey
     * @param mixed $data
     */
    private function setToCache( string $cacheKey, $data ): void
    {
        $dataCollection = null;
        try {
            $dataCollection = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $e ) {

        }

        if ( $dataCollection !== null ) {
            $dataCollection->set( $data );
            $this->cache->save( $dataCollection );
        }
    }

    private function createPolskiKraftCollection( array $data, string $cacheKey ): PolskiKraftDataCollection
    {
        $beers = $this->retrieveBestBeers( $data );

        $polskiKraftCollection = new PolskiKraftDataCollection();
        foreach ( $beers as $item ) {
            $polskiKraft = new PolskiKraftData( $item );
            $polskiKraftCollection->add( $polskiKraft->toArray() );
        }

        if ( $cacheKey !== null ) {
            $this->setToCache( $cacheKey, $polskiKraftCollection );
            $polskiKraftCollection->setCacheKey( $cacheKey );
        }

        return $polskiKraftCollection;
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
     * @param array $data
     *
     * @return array
     */
    private function retrieveBestBeers( array $data ): array
    {
        $this->filterData( $data );

        \usort(
            $data, static function ( $a, $b ) {
            return $b['rating'] <=> $a['rating'];
        }
        );

        $toShow = $notToShow = [];
        foreach ( $data as $item ) {
            $daysToLastUpdated = Carbon::now()
                ->diffInDays( Carbon::createFromTimestamp( $item['updated_at'] ) );
            if ( $daysToLastUpdated < self::LAST_UPDATED_DAYS_LIMIT ) {
                $toShow[] = $item;
            } elseif ( $daysToLastUpdated > self::LAST_UPDATED_DAYS_LIMIT && $daysToLastUpdated < self::LAST_UPDATED_MAX_DAYS ) {
                $notToShow[] = $item;
            }

            if ( \count( $toShow ) === self::BEERS_COUNT_TO_SHOW ) {
                return $toShow;
            }

        }

        $toShowCount = \count( $toShow );

        if ( $toShowCount < self::BEERS_COUNT_TO_SHOW && \count( $data ) >= 3 ) {
            $remaining = self::BEERS_COUNT_TO_SHOW - $toShowCount;
            $toAdd = \array_slice( $notToShow, 0, $remaining );
            foreach ( $toAdd as $item ) {
                $toShow[] = $item;
            }
        }

        return $toShow;
    }

    /**
     * TODO: Standalone service
     * TODO 2: WTF is with this foreach in foreach?
     *
     * Filters beers basing on answers, for example removes all beers with smoked tags from beer list
     * @param array $data
     *
     * @return void
     */
    private function filterData( array &$data ): void
    {
        $patterns = $this->getPregMatchPatterns();
        if ( $patterns === null ) {
            return;
        }

        foreach ( $data as $index => &$beer ) {
            foreach ( $beer['keywords'] as $keywordItem ) {
                foreach ( $patterns as $pattern ) {
                    if ( preg_match( $pattern, $keywordItem['keyword'] ) ) {
                        unset( $beer[$index] );
                    }
                }
            }
        }

    }

    private function getPregMatchPatterns(): ?array
    {
        $filters = null;
        if ( $this->answers->isSmoked( )) {
            $filters[] = 'smoked';
        }

        if ( $this->answers->isChocolate() ) {
            $filters[] = 'chocolate';
        }

        if ( $this->answers->isCoffee() ) {
            $filters[] = 'coffee';
        }

        if ( $this->answers->isSour() ) {
            $filters[] = 'sour';
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
