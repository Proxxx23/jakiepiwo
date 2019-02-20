<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use \DrewM\MailChimp\MailChimp;

class NewsletterRepository implements NewsletterRepositoryInterface
{
    /** @var \DrewM\MailChimp\MailChimp $mailChimp */
    protected $mailChimp;

    private const MAILCHIMP_LIST_ID = 'e51bd39480';

    private const MAILCHIMP_API_KEY = 'd42a6395b596459d1e2c358525a019b7-us3';

    /**
     * Constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        //TODO: DI?
        $this->mailChimp = new MailChimp( self::MAILCHIMP_API_KEY );
    }

    /**
     * @param string $email
     *
     * TODO: Return? Try-catch?
     */
    public function addToMailchimpSubscriptionList( string $email ): void
    {
        $this->mailChimp->post(
            'lists/' . self::MAILCHIMP_LIST_ID . '/members', [
                'email_address' => $email,
                'status' => 'pending',
            ]
        );
    }
}
