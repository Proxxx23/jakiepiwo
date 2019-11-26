<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface NewsletterRepositoryInterface
{
    /**
     * @param string $email
     */
    public function subscribe( string $email ): void;
}
