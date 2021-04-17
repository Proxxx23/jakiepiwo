<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class BeerData
{
    public function __construct(
        private array $answers,
        private ?array $avoidThis,
        private bool $barrelAged,
        private ?array $buyThis
    ) { }

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
