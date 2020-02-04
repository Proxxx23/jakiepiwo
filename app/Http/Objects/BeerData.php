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
    private bool $mailSent = false;
    private ?array $cacheKeys = null;
    private string $resultsHash;

    public function __construct(
        array $answers,
        ?array $avoidThis,
        bool $barrelAged,
        ?array $buyThis,
        bool $mustAvoid,
        bool $mustTake,
        ?string $username,
        string $resultsHash
    ) {
        $this->answers = $answers;
        $this->avoidThis = $avoidThis;
        $this->barrelAged = $barrelAged;
        $this->buyThis = $buyThis;
        $this->mustAvoid = $mustAvoid;
        $this->mustTake = $mustTake;
        $this->username = $username;
        $this->resultsHash = $resultsHash;
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
            $data['username'],
            $data['resultsHash']
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

    public function getResultsHash(): string
    {
        return $this->resultsHash;
    }

    public function getCacheKeys(): ?array
    {
        return $this->cacheKeys;
    }

    public function setMailSent( bool $mailSent ): void
    {
        $this->mailSent = $mailSent;
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
            'mailSent' => $this->mailSent,
            'cacheKeys' => $this->cacheKeys,
            'resultsHash' => $this->resultsHash,
        ];
    }

    private function completeCacheKeys( array $buyThis ): void
    {
        foreach ( $buyThis as $item ) {
            if ( $item['cacheKey'] !== null ) {
                $this->cacheKeys[] = $item['cacheKey'];
                continue;
            }
        }
    }
}
