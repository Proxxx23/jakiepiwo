<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use \DrewM\MailChimp\MailChimp;

final class NewsletterRepository implements NewsletterRepositoryInterface
{
    private MailChimp $mailChimp;

    public function __construct( MailChimp $mailChimp )
    {
        $this->mailChimp = $mailChimp;
    }

    public function subscribe( string $email ): void
    {
        $this->mailChimp->post(
            'lists/' . config('mail.mailchimpListId') . '/members', [
                'email_address' => $email,
                'status' => 'pending',
            ]
        );
    }
}
