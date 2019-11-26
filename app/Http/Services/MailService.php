<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\BeerData;
use App\Http\Objects\StylesToTakeCollection;

final class MailService
{
    /**
     * @param BeerData $proposedStyles
     * @param string|null $username
     * @param string $email
     */
    public function sendEmail( BeerData $proposedStyles, ?string $username, string $email ): void
    {
        $headers = 'From: Degustator <degustator@piwolucja.pl>' . "\r\n" .
            'Reply-To: Degustator <degustator@piwolucja.pl>' . "\r\n";

        if ( $username !== null ) {
            $subject = $username . ', oto najlepsze style piwne dla Ciebie!';
        } else {
            $subject = 'Oto najlepsze style piwne dla Ciebie!';
        }

        \mail( $email, $subject, $this->prepareEmailTemplate($proposedStyles), $headers );
    }

    /**
     * @param BeerData $proposedStyles
     *
     * @return string $mailTPL
     */
    private function prepareEmailTemplate(BeerData $proposedStyles): string
    {
        $mailTPL = 'Oto style, których powinieneś poszukiwać w sklepie:' . PHP_EOL;

        /** @var StylesToTakeCollection $style */
        foreach ( $proposedStyles->getBuyThis() as $style ) {
            $mailTPL .= $style['name'] . PHP_EOL;
        }

        $mailTPL .= PHP_EOL . 'Tych styli powinieneś unikać:' . PHP_EOL;
        /** @var StylesToTakeCollection $style */
        foreach ( $proposedStyles->getAvoidThis() as $style ) {
            $mailTPL .= $style['name'] . PHP_EOL;
        }

        return $mailTPL;
    }

}
