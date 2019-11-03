<?php
declare(strict_types=1);

namespace App\Http\Repositories;

interface BeersRepositoryInterface
{
    /**
     * @param string $ids
     * @return array
     */
    public function fetchByIds( string $ids ): array;
}