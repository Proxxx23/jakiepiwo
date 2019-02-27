<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface ScoringRepositoryInterface
{
    /**
     * @param int|null $questionNumber
     *
     * @return array|null
     */
    public function fetchScore(?int $questionNumber): ?array;
}
