<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class PolskiKraftBeerData
{
    private string $webUrl;
    private string $photoThumbnailUrl;
    private string $photoUrl;
    private string $title;
    private string $subtitle;
    private string $subtitleAlt;
    private int $rating;

    public function __construct( array $data )
    {
        $this->webUrl = $data['web_url'];
        $this->photoThumbnailUrl = $data['photo_thumbnail_url'];
        $this->photoUrl = $data['photo_url'];
        $this->title = $data['title'];
        $this->subtitle = $data['subtitle'];
        $this->subtitleAlt = $data['subtitle_alt'];
        $this->rating = $data['rating'];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function toArray(): array
    {
        return [
            'webUrl' => $this->webUrl,
            'photoThumbnailUrl' => $this->photoThumbnailUrl,
            'photoUrl' => $this->photoUrl,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'subtitleAlt' => $this->subtitleAlt,
            'rating' => $this->rating,
        ];
    }
}
