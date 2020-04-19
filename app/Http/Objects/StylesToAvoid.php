<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class StylesToAvoid extends AbstractStyles
{
    public function __construct( StyleInfo $styleInfo )
    {
        $this->description = $styleInfo->getDescription();
        $this->id = $styleInfo->getId();
        $this->name = $styleInfo->getName();
        $this->otherName = $styleInfo->getOtherName();
        $this->polishName = $styleInfo->getPolishName();
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'id' => $this->id,
            'name' => $this->name,
            'otherName' => $this->otherName,
            'polishName' => $this->polishName,
        ];
    }
}
