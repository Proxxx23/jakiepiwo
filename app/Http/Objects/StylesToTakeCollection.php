<?php
declare(strict_types=1);

namespace App\Http\Objects;

use Illuminate\Support\Collection;

final class StylesToTakeCollection extends Collection
{
    /** @var array */
    private $idStylesToTake;

    /**
     * @param array $idStylesToTake
     * @return StylesToTakeCollection
     */
    public function setIdStylesToTake( array $idStylesToTake ): StylesToTakeCollection
    {
        $this->idStylesToTake = $idStylesToTake;

        return $this;
    }

    /**
     * @return array
     */
    public function getIdStylesToTake(): array
    {
        return $this->idStylesToTake;
    }
}
