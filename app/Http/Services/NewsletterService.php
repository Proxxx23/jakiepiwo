<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\NewsletterRepositoryInterface;
use App\Http\Utils\ValidationUtils;

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
     * @param string|null $email
     * @param int $newsletter
     *
     * @return bool
     * @throws \Exception
     */
    public function addToNewsletterList( ?string $email, int $newsletter ): bool
    {
        if ( $newsletter === 1 && $email === null ) {
//            $this->logError( 'Jeżeli chcesz dopisać się do newslettera, musisz podać adres e-mail.' );
        }

        if ( ValidationUtils::validateEmail( $email ) ) {
            $this->addEmailToNewsletterList( $email );
            return true;
        }

        return false;
    }

    /**
     * @documentation: https://github.com/drewm/mailchimp-api
     *
     * @param string $email
     *
     * @throws \Exception
     */
    protected function addEmailToNewsletterList( string $email ): void
    {
        $this->newsletterRepository->addToMailchimpSubscriptionList( $email );
    }
}
