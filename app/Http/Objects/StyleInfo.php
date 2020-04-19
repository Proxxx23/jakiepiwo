<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class StyleInfo
{
    private ?string $description;
    private int $id;
    private string $name;
    private ?string $polishName;
    private ?string $otherName;

    private function __construct(
        ?string $description,
        int $id,
        string $name,
        ?string $polishName,
        ?string $otherName
    ) {
        $this->description = \is_string( $description ) ? \trim( $description ) : null;
        $this->id = $id;
        $this->name = $name;
        $this->polishName = \is_string( $polishName ) ? \trim( $polishName ) : null;
        $this->otherName = \is_string( $otherName ) ? \trim( $otherName ) : null;
    }

    public static function fromArray( array $data, int $id ): self
    {
        return new self(
            $data['description'],
            $id,
            $data['name'],
            $data['polishName'],
            $data['otherName'],
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMoreUrl(): ?string
    {
        return $this->moreUrl;
    }
}
