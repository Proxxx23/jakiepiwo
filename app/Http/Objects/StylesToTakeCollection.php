<?php
declare(strict_types=1);

namespace App\Http\Objects;

use Illuminate\Support\Collection;

final class StylesToTakeCollection extends Collection
{
    /** @var array */
    private array $idStylesToTake;

    public function setIdStylesToTake( array $idStylesToTake ): StylesToTakeCollection
    {
        $this->idStylesToTake = $idStylesToTake;

        return $this;
    }

    public function getIdStylesToTake(): array
    {
        return $this->idStylesToTake;
    }
}
