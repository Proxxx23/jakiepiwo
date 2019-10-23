<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use \DrewM\MailChimp\MailChimp;

final class NewsletterRepository implements NewsletterRepositoryInterface
{
    /** @var \DrewM\MailChimp\MailChimp $mailChimp */
    private $mailChimp;

    /**
     * @param MailChimp $mailChimp
     */
    public function __construct( MailChimp $mailChimp )
    {
        $this->mailChimp = $mailChimp;
    }

    /**
     * @param string $email
     *
     * TODO: Return? Try-catch?
     */
    public function subscribeToEmailList( string $email ): void
    {
        $this->mailChimp->post(
            'lists/' . config('mail.mailchimpListId') . '/members', [
                'email_address' => $email,
                'status' => 'pending',
            ]
        );
    }
}
