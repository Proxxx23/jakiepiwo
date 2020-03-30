<?php
declare( strict_types=1 );

namespace App\Http\Objects;

abstract class AbstractStyles
{
    protected int $id;
    protected string $name;
    protected ?string $otherName;
    protected ?string $polishName;
    protected ?string $description;

    abstract protected function toArray(): array;
}
