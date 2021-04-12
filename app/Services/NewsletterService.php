<?php
declare( strict_types=1 );

namespace App\Services;

use App\Http\Repositories\NewsletterRepositoryInterface;

final class NewsletterService
{
    private NewsletterRepositoryInterface $newsletterRepository;

    public function __construct( NewsletterRepositoryInterface $newsletterRepository )
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @see: https://github.com/drewm/mailchimp-api
     *
     * @param string $email
     *
     * @throws \Exception
     */
    public function addToNewsletterList( string $email ): void
    {
        $this->newsletterRepository->subscribe( $email );
    }
}
