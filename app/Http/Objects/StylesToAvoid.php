<?php
declare(strict_types=1);

namespace App\Http\Objects;

final class StylesToAvoid
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var null|string */
    private $otherName;
    /** @var null|string */
    private $polishName;

    /**
     * StylesToTake constructor.
     * @param object $data
     */
    public function __construct( object $data )
    {
        $this->id = (int) $data->id;
        $this->name = $data->name;
        $this->otherName = $data->name2;
        $this->polishName = $data->name_pl;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
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
