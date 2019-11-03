<?php
declare( strict_types=1 );

namespace App\Http\Services;

// TODO
final class MailService
{
    /**
     * @param string $proposedStyles
     * @param string|null $username
     * @param string $email
     */
    public function sendEmail( string $proposedStyles, ?string $username, string $email ): void
    {
        $headers = 'From: thegustator@piwolucja.pl' . "\r\n" .
            'Reply-To: thegustator@piwolucja.pl' . "\r\n";

        if ( $username !== null ) {
            $subject = $username . ', oto najlepsze style piwne dla Ciebie!';
        } else {
            $subject = 'Oto najlepsze style piwne dla Ciebie!';
        }

        \mail( $email, $subject, $this->prepareEmailTemplate($proposedStyles), $headers );
    }

    /**
     * @param string $proposedStyles
     *
     * @return string $mailTPL
     */
    private function prepareEmailTemplate(string $proposedStyles): string
    {
        $proposedStyles = \json_decode($proposedStyles, true, 512, JSON_THROW_ON_ERROR);

        $mailTPL = 'Oto style, których powinieneś poszukiwać w sklepie:' . PHP_EOL;
        foreach ($proposedStyles['buyThis'] as $style) {
            $mailTPL .= $style['name'];
        }

        $mailTPL .= '';

        return $mailTPL;
    }

}
