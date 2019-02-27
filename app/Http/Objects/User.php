<?php
declare(strict_types=1);

namespace App\Http\Objects;

class User extends BaseObject
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
        $this->options = $options;
    }
}
