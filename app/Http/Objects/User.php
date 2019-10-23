<?php
declare( strict_types=1 );

namespace App\Http\Objects;

/**
 * @method setUsername( ?string $username )
 * @method setEmail( ?string $email )
 * @method setSendEmail ( bool $sendEmail )
 * @method setAddToNewsletterList( bool $newsletter )
 * @method getUsername()
 * @method getEmail()
 * @method getSendEmail()
 * @method getAddToNewsletterList()
 * @method setOptions( OptionsInterface $options )
 * @method getOptions()
 */
final class User extends AbstractFixedPropertyObject
{
    /** @var string|null */
    protected $username;
    /** @var string|null */
    protected $email;
    /** @var bool */
    protected $sendEmail = false;
    /** @var bool */
    protected $addToNewsletterList = false;
    /** @var OptionsInterface */
    protected $options;

    public function __construct( OptionsInterface $options )
    {
        $this->setOptions( $options );
    }
}
