<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

final class BeersRepository implements BeersRepositoryInterface
{
    public function fetchByIds( array $ids, bool $shuffle = false ): array
    {
        $randomizeOrder = ( $shuffle === true )
            ? 'ORDER BY RAND()'
            : '';

        try {
            return DB::select(
                "SELECT `id`, 
                    `name`, 
                    `name2`, 
                    `name_pl` 
                FROM 
                    beers 
                WHERE 
                    id 
                IN (" . \implode( ',', $ids ) . ')' . $randomizeOrder
            );
        } catch ( \Exception $exception ) {
            return [];
        }
    }
}
