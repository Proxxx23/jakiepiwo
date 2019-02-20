<?php
declare( strict_types=1 );

interface NewsletterRepositoryInterface
{
    /**
     * @param string $email
     */
    public function addToMailchimpSubscriptionList( string $email ): void;
}
