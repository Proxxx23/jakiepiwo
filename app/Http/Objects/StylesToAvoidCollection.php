<?php
declare( strict_types=1 );

namespace App\Http\Objects;

use Illuminate\Support\Collection;

final class StylesToAvoidCollection extends Collection
{
    private array $idStylesToAvoid;

    public function setIdStylesToAvoid( array $idStylesToAvoid ): StylesToAvoidCollection
    {
        $this->idStylesToAvoid = $idStylesToAvoid;

        return $this;
    }

    public function getIdStylesToAvoid(): array
    {
        return $this->idStylesToAvoid;
    }
}
