<?php
declare( strict_types=1 );

namespace App\Http\Objects;

/**
 * @method setUsername( ?string $username )
 * @method setEmail( ?string $email )
 * @method setNewsletterOpt( int $int )
 * @method getUsername()
 * @method getEmail()
 * @method getNewsletterOpt()
 * @method setOptions( OptionsInterface $options )
 * @method getOptions()
 */
class User extends AbstractFixedPropertyObject
{
    /** @var string|null */
    protected $username;
    /** @var string|null */
    protected $email;
    /** @var int */
    protected $newsletterOpt = 0;

    /** @var OptionsInterface */
    protected $options;

    public function __construct( OptionsInterface $options )
    {
        $this->setOptions( $options );
    }
}
