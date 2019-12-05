<?php
declare(strict_types=1);

namespace App\Http\Objects;

final class StylesToTake
{
    private ?PolskiKraftDataCollection $beerDataCollection;
    private int $id;
    private string $name;
    private ?string $otherName;
    private ?string $polishName;

    public function __construct(
        object $styleInfo,
        ?PolskiKraftDataCollection $beerDataCollection
    ) {
        $this->beerDataCollection = $beerDataCollection;
        $this->id = (int) $styleInfo->id;
        $this->name = $styleInfo->name;
        $this->otherName = $styleInfo->name2;
        $this->polishName = $styleInfo->name_pl;
    }

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
