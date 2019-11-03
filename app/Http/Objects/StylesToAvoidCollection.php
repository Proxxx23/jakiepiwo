<?php
declare(strict_types=1);

namespace App\Http\Objects;

use Illuminate\Support\Collection;

final class StylesToAvoidCollection extends Collection
{
    /** @var array */
    private $idStylesToAvoid;

    /**
     * @param array $idStylesToAvoid
     *
     * @return StylesToAvoidCollection
     */
    public function setIdStylesToAvoid( array $idStylesToAvoid ): StylesToAvoidCollection
    {
        $this->idStylesToAvoid = $idStylesToAvoid;

        return $this;
    }

    /**
     * @return array
     */
    public function getIdStylesToAvoid(): array
    {
        return $this->idStylesToAvoid;
    }
}
