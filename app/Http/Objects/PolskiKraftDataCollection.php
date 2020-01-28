<?php
declare(strict_types=1);

namespace App\Http\Objects;

use Illuminate\Support\Collection;

final class PolskiKraftDataCollection extends Collection
{
    private ?string $cacheKey = null;

    public function getCacheKey(): ?string
    {
        return $this->cacheKey;
    }

    public function setCacheKey( ?string $cacheKey ): void
    {
        $this->cacheKey = $cacheKey;
    }
}
