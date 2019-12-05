<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface NewsletterRepositoryInterface
{
    public function subscribe( string $email ): void;
}
