<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class BeerData
{
    /** @var string */
    private $webUrl;
    /** @var string */
    private $photoThumbnailUrl;
    /** @var string */
    private $photoUrl;
    /** @var string */
    private $title;
    /** @var string */
    private $subtitle;
    /** @var string */
    private $subtitleAlt;
    /** @var int */
    private $rating;

    /**
     * @param array $data
     */
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

    /**
     * @return array
     */
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
