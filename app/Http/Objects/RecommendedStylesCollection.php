<?php
declare( strict_types=1 );

namespace App\Http\Objects;

use Illuminate\Support\Collection;

final class RecommendedStylesCollection extends Collection
{
    private ?array $recommendedIds;

    public function setRecommendedIds( ?array $recommendedIds ): RecommendedStylesCollection
    {
        $this->recommendedIds = $recommendedIds;

        return $this;
    }

    public function getRecommendedIds(): ?array
    {
        return $this->recommendedIds;
    }
}
