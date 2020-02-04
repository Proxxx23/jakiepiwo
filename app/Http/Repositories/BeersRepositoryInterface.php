<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\StyleInfoCollection;

interface BeersRepositoryInterface
{
    public function fetchByIds( array $ids ): ?StyleInfoCollection;
}
