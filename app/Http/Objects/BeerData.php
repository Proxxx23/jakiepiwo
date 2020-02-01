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

    public function __construct( array $data )
    {
        $this->answers = $data['answers'];
        $this->avoidThis = $data['avoidThis'];
        $this->barrelAged = $data['barrelAged'];
        $this->buyThis = $data['buyThis'];
        $this->mustAvoid = $data['mustAvoid'];
        $this->mustTake = $data['mustTake'];
        $this->username = $data['username'];
        $this->completeCacheKeys( $data['buyThis'] ); //todo: unit test
    }

    //todo: from Array named constructor

    public function getBuyThis(): ?array
    {
        return $this->buyThis;
    }

    public function getAvoidThis(): ?array
    {
        return $this->avoidThis;
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
        ];
    }

    private function completeCacheKeys( array $buyThis ): void
    {
        foreach ($buyThis as $item) {
            if ( $item['cacheKey'] !== null ) {
                $this->cacheKeys[] = $item['cacheKey'];
                continue;
            }
        }
    }
}
