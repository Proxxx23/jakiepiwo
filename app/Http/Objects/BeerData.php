<?php
declare( strict_types=1 );

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
    private ?array $cacheKeys = null;

    public function __construct(
        array $answers,
        ?array $avoidThis,
        bool $barrelAged,
        ?array $buyThis,
        bool $mustAvoid,
        bool $mustTake,
        ?string $username
    ) {
        $this->answers = $answers;
        $this->avoidThis = $avoidThis;
        $this->barrelAged = $barrelAged;
        $this->buyThis = $buyThis;
        $this->mustAvoid = $mustAvoid;
        $this->mustTake = $mustTake;
        $this->username = $username;
        $this->completeCacheKeys( $buyThis );
    }

    public static function fromArray( array $data ): self
    {
        return new self(
            $data['answers'],
            $data['avoidThis'],
            $data['barrelAged'],
            $data['buyThis'],
            $data['mustAvoid'],
            $data['mustTake'],
            $data['username']
        );
    }

    public function getBuyThis(): ?array
    {
        return $this->buyThis;
    }

    public function getAvoidThis(): ?array
    {
        return $this->avoidThis;
    }

    public function getCacheKeys(): ?array
    {
        return $this->cacheKeys;
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
            'cacheKeys' => $this->cacheKeys,
        ];
    }

    private function completeCacheKeys( ?array $buyThis ): void
    {
        if ( $buyThis === null ) {
            return;
        }

        foreach ( $buyThis as $item ) {
            if ( $item['cacheKey'] !== null ) {
                $this->cacheKeys[] = $item['cacheKey'];
                continue;
            }
        }
    }
}
