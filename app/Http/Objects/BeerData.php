<?php
declare(strict_types=1);

namespace App\Http\Objects;

final class BeerData
{
    /** @var array */
    private $answers;
    /** @var array|null */
    private $avoidThis;
    /** @var bool */
    private $barrelAged;
    /** @var array|null */
    private $buyThis;
    /** @var bool */
    private $mustAvoid;
    /** @var bool */
    private $mustTake;
    /** @var string|null */
    private $username;

    /**
     * @param array $data
     */
    public function __construct( array $data )
    {
        $this->answers = $data['answers'];
        $this->avoidThis = $data['avoidThis'];
        $this->barrelAged = $data['barrelAged'];
        $this->buyThis = $data['buyThis'];
        $this->mustAvoid = $data['mustAvoid'];
        $this->mustTake = $data['mustTake'];
        $this->username = $data['username'];
    }

    /**
     * @return array|null
     */
    public function getBuyThis(): ?array
    {
        return $this->buyThis;
    }

    /**
     * @return array|null
     */
    public function getAvoidThis(): ?array
    {
        return $this->avoidThis;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return \json_encode([
            'answers' => $this->answers,
            'avoidThis' => $this->avoidThis,
            'barrelAged' => $this->barrelAged,
            'buyThis' => $this->buyThis,
            'mustAvoid' => $this->mustAvoid,
            'mustTake' => $this->mustTake,
            'username' => $this->username,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE, 512);
    }
}