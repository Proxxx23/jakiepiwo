<?php
declare(strict_types=1);

namespace App\Http\Repositories;

interface OnTapRepositoryInterface
{
    /**
     * @param string $beerName
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchTapsByBeerName( string $beerName ): ?array;
}