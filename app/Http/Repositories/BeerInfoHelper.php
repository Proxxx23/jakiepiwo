<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftData;
use App\Http\Objects\PolskiKraftDataCollection;
use App\Http\Utils\Dictionary;
use GuzzleHttp\ClientInterface;

final class BeerInfoHelper implements PolskiKraftRepositoryInterface
{
    private const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';

    private Dictionary $dictionary;
    private OnTapRepositoryInterface $onTapRepository;
    private ClientInterface $httpClient;

    public function __construct(
        Dictionary $dictionary,
        OnTapRepositoryInterface $onTapRepository,
        ClientInterface $httpClient )
    {
        $this->dictionary = $dictionary;
        $this->onTapRepository = $onTapRepository;
        $this->httpClient = $httpClient;
    }

    /**
     * @param int $beerId
     *
     * @return PolskiKraftDataCollection|null
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @todo: guzzle
     */
    public function fetchByBeerId( int $beerId ): ?PolskiKraftDataCollection
    {
        if ( !\array_key_exists( $beerId, $this->dictionary->get() ) ) {
            return null;
        }

        $translatedBeerId = $this->dictionary->getById( $beerId );

        $url = 'https://www.polskikraft.pl/openapi/style/' . $translatedBeerId . '/examples';
        $response = $this->httpClient->request('GET', $url);
        if ( $response->getStatusCode() !== 200 ) {
            return null; //todo: any message
        }

        $data = \json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        if ( empty( $data ) ) {
            return null;
        }

        while ( \count( $data ) > 3 ) {
            $randomIdToDelete = \random_int( 0, \count( $data ) - 1 );
            unset( $data[$randomIdToDelete] );
        }

        $polskiKraftCollection = new PolskiKraftDataCollection();
        foreach ( $data as $item ) {
            $polskiKraft = new PolskiKraftData( $item );

            if ( $this->onTapRepository->connected() ) {
                $onTap = $this->onTapRepository->fetchTapsByBeerData( $polskiKraft );
                $polskiKraft->setOnTap( $onTap );
            }

            $polskiKraftCollection->add( $polskiKraft->toArray() );
        }

        return $polskiKraftCollection;
    }
}
