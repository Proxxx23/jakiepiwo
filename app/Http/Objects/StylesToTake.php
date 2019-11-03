<?php
declare(strict_types=1);

namespace App\Http\Objects;

final class StylesToTake
{
    /** @var PolskiKraftBeerDataCollection|null */
    private $beerDataCollection;
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var null|string */
    private $otherName;
    /** @var null|string */
    private $polishName;

    /**
     * @param object $styleInfo
     * @param PolskiKraftBeerDataCollection|null $beerDataCollection
     */
    public function __construct(
        object $styleInfo,
        ?PolskiKraftBeerDataCollection $beerDataCollection
    ) {
        $this->beerDataCollection = $beerDataCollection;
        $this->id = (int) $styleInfo->id;
        $this->name = $styleInfo->name;
        $this->otherName = $styleInfo->name2;
        $this->polishName = $styleInfo->name_pl;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'beerDataCollection' => $this->beerDataCollection !== null ? $this->beerDataCollection->toArray() : null,
            'id' => $this->id,
            'name' => $this->name,
            'otherName' => $this->otherName,
            'polishName' => $this->polishName,
        ];
    }
}
