<?php
declare(strict_types=1);

namespace App\Http\Objects;

class StylesToTake extends AbstractFixedPropertyObject
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $flavourId;
    /** @var string */
    protected $name;
    /** @var null|string */
    protected $otherName;
    /** @var null|string */
    protected $polishName;
    /** @var BeersCollection */
    protected $beersCollection;

    /**
     * StylesToTake constructor.
     * @param object $data
     */
    public function __construct(object $data)
    {
        $this->id = (int) $data->id;
        $this->flavourId = (int) $data->id_flavour;
        $this->name = $data->name;
        $this->otherName = $data->name2;
        $this->polishName = $data->name_pl;
    }
}