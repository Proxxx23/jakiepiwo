<?php
declare(strict_types=1);

namespace App\Http\Repositories;

interface BeersRepositoryInterface
{
    /**
     * @param array $ids
     * @return array
     */
    public function fetchByIds( array $ids ): array;
}