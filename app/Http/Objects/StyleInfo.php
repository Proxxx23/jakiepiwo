<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class StyleInfo
{
    private array $description;
    private int $id;
    private ?string $moreUrlQuery;
    private string $name;
    private ?string $otherName;
    private ?string $polishName;

    private function __construct(
        array $description,
        int $id,
        ?string $moreUrlQuery,
        string $name,
        ?string $otherName,
        ?string $polishName
    ) {
        $this->description = $description;
        $this->id = $id;
        $this->moreUrlQuery = $moreUrlQuery;
        $this->name = $name;
        $this->otherName = \is_string( $otherName ) ? \trim( $otherName ) : null;
        $this->polishName = \is_string( $polishName ) ? \trim( $polishName ) : null;

    }

    public static function fromArray( array $data, int $id ): self
    {
        return new self(
            $data['description'],
            $id,
            $data['moreUrlQuery'],
            $data['name'],
            $data['otherName'],
            $data['polishName'],
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSmokedNames(): void
    {
        $this->name = '(Smoked) ' . $this->name;

        $smoking = $this->polishName !== null && \preg_match('/porter|stout|koźlak/ui', $this->polishName)
            ? '(Wędzony) '
            : '(Wędzone) ';

        $this->polishName = $smoking . $this->polishName;
    }

    public function getOtherName(): ?string
    {
        return $this->otherName;
    }

    public function getPolishName(): ?string
    {
        return $this->polishName;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function getMoreUrlQuery(): ?string
    {
        return $this->moreUrlQuery;
    }
}
