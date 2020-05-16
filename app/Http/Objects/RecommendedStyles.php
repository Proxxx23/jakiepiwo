<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class RecommendedStyles extends AbstractStyles
{
    private ?PolskiKraftDataCollection $beerDataCollection;
    private string $cacheKey = '';
    private bool $highlighted = false;
    private ?string $moreUrlQuery;

    public function __construct( StyleInfo $styleInfo, ?PolskiKraftDataCollection $beerDataCollection )
    {
        $this->beerDataCollection = $beerDataCollection;
        if ( $beerDataCollection !== null ) {
            $this->cacheKey = $beerDataCollection->getCacheKey();
        }
        $this->description = $styleInfo->getDescription();
        $this->id = $styleInfo->getId();
        $this->moreUrlQuery = $styleInfo->getMoreUrlQuery();
        $this->name = $styleInfo->getName();
        $this->otherName = $styleInfo->getOtherName();
        $this->polishName = $styleInfo->getPolishName();
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
            'moreUrlQuery' => $this->moreUrlQuery,
            'name' => $this->name,
            'otherName' => $this->otherName,
            'polishName' => $this->polishName,
        ];
    }
}
