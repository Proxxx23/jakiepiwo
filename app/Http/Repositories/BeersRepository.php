<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

final class BeersRepository implements BeersRepositoryInterface
{
    /**
     * @param array $ids
     * @return array
     */
    public function fetchByIds( array $ids ): array
    {
        return DB::select(
            "SELECT `id`, 
                    `name`, 
                    `name2`, 
                    `name_pl` 
            FROM 
                    beers 
            WHERE 
                  id 
            IN (" . implode( ',', $ids ) . ')');
    }
}