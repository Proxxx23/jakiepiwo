<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class PolskiKraftData
{
    private string $webUrl;
    private string $photoThumbnailUrl;
    private string $photoUrl;
    private string $title;
    private string $subtitle;
    private string $subtitleAlt;
    private float $rating;

    public function __construct( array $data )
    {
        $this->webUrl = $data['web_url'];
        $this->photoThumbnailUrl = $data['photo_thumbnail_url'];
        $this->photoUrl = $data['photo_url'];
        $this->title = \trim( $data['title'] );
        $this->subtitle = \trim( $data['subtitle'] );
        $this->subtitleAlt = \trim( $data['subtitle_alt'] );
        $this->rating = $data['rating'];
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
