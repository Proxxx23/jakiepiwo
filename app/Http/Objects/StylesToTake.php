<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class StylesToTake extends AbstractStyles
{
    private ?PolskiKraftDataCollection $beerDataCollection;
    private ?string $cacheKey = null;
    private bool $highlighted = false;
    private ?string $moreUrl;

    public function __construct( StyleInfo $styleInfo, ?PolskiKraftDataCollection $beerDataCollection )
    {
        $this->beerDataCollection = $beerDataCollection;
        if ( $beerDataCollection !== null ) {
            $this->cacheKey = $beerDataCollection->getCacheKey();
        }
        $this->description = $styleInfo->getDescription();
        $this->id = $styleInfo->getId();
        $this->name = $styleInfo->getName();
        $this->otherName = $styleInfo->getOtherName();
        $this->polishName = $styleInfo->getPolishName();
        $this->moreUrl = $styleInfo->getMoreUrl();
    }

    public function setHighlighted( bool $highlighted ): void
    {
        $this->highlighted = $highlighted;
    }

    public function toArray(): array
    {
        return [
            'beerDataCollection' => $this->beerDataCollection !== null ? $this->beerDataCollection->toArray() : null,
            'cacheKey' => $this->cacheKey,
            'description' => $this->description,
            'highlighted' => $this->highlighted,
            'id' => $this->id,
            'name' => $this->name,
            'otherName' => $this->otherName,
            'polishName' => $this->polishName,
            'moreUrl' => $this->moreUrl,
        ];
    }
}
