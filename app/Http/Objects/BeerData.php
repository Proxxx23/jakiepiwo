<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class BeerData
{
    private array $answers;
    private ?array $avoidThis;
    private bool $barrelAged;
    private ?array $buyThis;

    public function __construct(
        array $answers,
        ?array $avoidThis,
        bool $barrelAged,
        ?array $buyThis
    ) {
        $this->answers = $answers;
        $this->avoidThis = $avoidThis;
        $this->barrelAged = $barrelAged;
        $this->buyThis = $buyThis;
    }

    public static function fromArray( array $data ): self
    {
        return new self(
            $data['answers'],
            $data['avoidThis'],
            $data['barrelAged'],
            $data['buyThis'],
        );
    }

    public function toArray(): array
    {
        return [
            'answers' => $this->answers,
            'avoidThis' => $this->avoidThis,
            'barrelAged' => $this->barrelAged,
            'buyThis' => $this->buyThis,
        ];
    }
}
