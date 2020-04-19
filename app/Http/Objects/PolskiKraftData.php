<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class PolskiKraftData
{
    private string $photoThumbnailUrl;
    private string $photoUrl;
    private float $rating;
    private string $subtitle;
    private string $subtitleAlt;
    private string $title;
    private string $webUrl;

    public function __construct( array $data )
    {
        $this->photoThumbnailUrl = $data['photo_thumbnail_url'];
        $this->photoUrl = $data['photo_url'];
        $this->rating = $data['rating'];
        $this->subtitle = \trim( $data['subtitle'] );
        $this->subtitleAlt = \trim( $data['subtitle_alt'] );
        $this->title = \trim( $data['title'] );
        $this->webUrl = $data['web_url'];
    }

    public function toArray(): array
    {
        return [
            'photoThumbnailUrl' => $this->photoThumbnailUrl,
            'photoUrl' => $this->photoUrl,
            'rating' => $this->rating,
            'subtitle' => $this->subtitle,
            'subtitleAlt' => $this->subtitleAlt,
            'title' => $this->title,
            'webUrl' => $this->webUrl,
        ];
    }
}
