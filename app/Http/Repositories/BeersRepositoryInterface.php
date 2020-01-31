<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface BeersRepositoryInterface
{
    public function fetchByIds( array $ids, bool $shuffle = false ): array;
}
