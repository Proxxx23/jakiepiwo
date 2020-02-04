<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class StylesToTake
{
    private ?PolskiKraftDataCollection $beerDataCollection;
    private ?string $cacheKey = null;

    public function __construct( StyleInfo $styleInfo, ?PolskiKraftDataCollection $beerDataCollection )
    {
        $this->beerDataCollection = $beerDataCollection;
        if ( $beerDataCollection !== null ) {
            $this->cacheKey = $beerDataCollection->getCacheKey();
        }
        $this->id = $styleInfo->getId();
        $this->name = $styleInfo->getName();
        $this->otherName = $styleInfo->getOtherName();
        $this->polishName = $styleInfo->getPolishName();
        $this->description = $styleInfo->getDescription();
        $this->moreLink = $styleInfo->getMoreLink();
    }

    public function toArray(): array
    {
        return [
            'beerDataCollection' => $this->beerDataCollection !== null ? $this->beerDataCollection->toArray() : null,
            'cacheKey' => $this->cacheKey,
            'id' => $this->id,
            'name' => $this->name,
            'otherName' => $this->otherName,
            'polishName' => $this->polishName,
            'description' => $this->description,
            'moreLink' => $this->moreLink,
        ];
    }
}
