<?php
declare(strict_types=1);

namespace App\Http\Objects;

final class StylesToAvoid
{
    private int $id;
    private string $name;
    private ?string $otherName;
    private ?string $polishName;

    public function __construct( object $data )
    {
        $this->id = (int) $data->id;
        $this->name = $data->name;
        $this->otherName = $data->name2;
        $this->polishName = $data->name_pl;
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
        ];
    }
}
