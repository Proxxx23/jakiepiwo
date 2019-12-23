<?php
declare(strict_types=1);

namespace App\Http\Objects;

final class BeerData
{
    private array $answers;
    private ?array $avoidThis;
    private bool $barrelAged;
    private ?array $buyThis;
    private bool $mustAvoid;
    private bool $mustTake;
    private ?string $username;

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

    public function getBuyThis(): ?array
    {
        return $this->buyThis;
    }

    public function getAvoidThis(): ?array
    {
        return $this->avoidThis;
    }

    public function toArray(): array
    {
        return [
            'answers' => $this->answers,
            'avoidThis' => $this->avoidThis,
            'barrelAged' => $this->barrelAged,
            'buyThis' => $this->buyThis,
            'mustAvoid' => $this->mustAvoid,
            'mustTake' => $this->mustTake,
            'username' => $this->username,
        ];
    }

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