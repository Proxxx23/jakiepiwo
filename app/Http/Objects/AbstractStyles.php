<?php
declare( strict_types=1 );

namespace App\Http\Objects;

abstract class AbstractStyles
{
    protected array $description;
    protected int $id;
    protected string $name;
    protected ?string $otherName;
    protected ?string $polishName;


    abstract protected function toArray(): array;
}
