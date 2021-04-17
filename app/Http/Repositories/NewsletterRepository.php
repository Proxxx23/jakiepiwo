<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use \DrewM\MailChimp\MailChimp;

final class NewsletterRepository implements NewsletterRepositoryInterface
{
    public function __construct( private MailChimp $mailChimp )
    { }

    public function subscribe( string $email ): void
    {
        $this->mailChimp->post(
            'lists/' . \config( 'mail.mailchimpAudienceId' ) . '/members', [
                'email_address' => $email,
                'status' => 'pending',
            ]
        );
    }
}
