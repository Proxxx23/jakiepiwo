<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class StyleInfo
{
    private int $id;
    private string $name;
    private ?string $otherName;
    private ?string $polishName;
    private ?string $description;
    private ?string $moreLink;

    private function __construct(
        int $id,
        string $name,
        ?string $secondName,
        ?string $polishName,
        ?string $description,
        ?string $moreLink
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->otherName = \is_string( $secondName ) ? \trim( $secondName ) : null;
        $this->polishName = \is_string( $polishName ) ? \trim( $polishName ) : null;
        $this->description = \is_string( $description ) ? \trim( $description ) : null;
        $this->moreLink = $moreLink;
    }

    public static function fromArray( array $data, int $id ): self
    {
        return new self(
            $id,
            $data['name'],
            $data['otherName'],
            $data['polishName'],
            $data['description'],
            $data['moreLink']
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

        $smoking = \strpos($this->polishName, 'porter') !== false
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

    public function getMoreLink(): ?string
    {
        return $this->moreLink;
    }
}
