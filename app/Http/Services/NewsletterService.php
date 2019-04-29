<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\NewsletterRepositoryInterface;

class NewsletterService
{
    /** @var NewsletterRepositoryInterface */
    protected $newsletterRepository;

    /**
     * Constructor.
     *
     * @param NewsletterRepositoryInterface $newsletterRepository
     */
    public function __construct( NewsletterRepositoryInterface $newsletterRepository )
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @documentation: https://github.com/drewm/mailchimp-api
     *
     * @param string|null $email
     *
     * @throws \Exception
     */
    public function addToNewsletterList( ?string $email ): void
    {
        $this->newsletterRepository->addToMailchimpSubscriptionList( $email );
    }
}
