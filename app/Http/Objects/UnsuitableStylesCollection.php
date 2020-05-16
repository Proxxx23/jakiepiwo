<?php
declare( strict_types=1 );

namespace App\Http\Objects;

use Illuminate\Support\Collection;

final class UnsuitableStylesCollection extends Collection
{
    private ?array $unsuitableIds;

    public function setUnsuitableIds( ?array $unsuitableIds ): UnsuitableStylesCollection
    {
        $this->unsuitableIds = $unsuitableIds;

        return $this;
    }

    public function getUnsuitableIds(): ?array
    {
        return $this->unsuitableIds;
    }
}
