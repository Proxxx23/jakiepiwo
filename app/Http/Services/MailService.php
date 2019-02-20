<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Utils\ValidationUtils;

class MailService
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function sendEmail( string $email ): bool
    {
        $headers = 'From: jakiepiwomamwybrac@piwolucja.pl' . "\r\n" .
            'Reply-To: jakiepiwomamwybrac@piwolucja.pl' . "\r\n";

        $subject = $_POST['username'] . ', oto 3 najlepsze style dla Ciebie!';

        if ( ValidationUtils::emailIsValid($email) ) {
            \mail( $email, $subject, $this->prepareEmailTemplate(), $headers );
            return true;
        }

//        $this->logError( 'Błędny adres e-mail!' );

        return false;
    }

    /**
     * Prepares a TPL for an e-mail
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
