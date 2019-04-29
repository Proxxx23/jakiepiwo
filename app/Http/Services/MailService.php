<?php
declare( strict_types=1 );

namespace App\Http\Services;

class MailService
{
    /**
     * @param string $email
     */
    public function sendEmail( string $email ): void
    {
        $headers = 'From: jakiepiwomamwybrac@piwolucja.pl' . "\r\n" .
            'Reply-To: jakiepiwomamwybrac@piwolucja.pl' . "\r\n";

        $subject = $_POST['username'] . ', oto 3 najlepsze style piwne dla Ciebie!';

        \mail( $email, $subject, $this->prepareEmailTemplate(), $headers );
    }

    /**
     * @return string $mailTPL
     */
    protected function prepareEmailTemplate(): string
    {
        $mailTPL = '';
        $mailTPL .= '';
        $mailTPL .= '';

        return $mailTPL;
    }

}
