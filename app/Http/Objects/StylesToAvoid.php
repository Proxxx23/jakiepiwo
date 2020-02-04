<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class StylesToAvoid extends AbstractStyles
{
    public function __construct( StyleInfo $styleInfo )
    {
        $this->id = $styleInfo->getId();
        $this->name = $styleInfo->getName();
        $this->otherName = $styleInfo->getOtherName();
        $this->polishName = $styleInfo->getPolishName();
        $this->description = $styleInfo->getDescription();
        $this->moreLink = $styleInfo->getMoreLink();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'otherName' => $this->otherName,
            'polishName' => $this->polishName,
            'description' => $this->description,
            'moreLink' => $this->moreLink,
        ];
    }
}
