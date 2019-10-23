<?php
declare( strict_types=1 );

namespace App\Http\Objects;

use App\Http\Utils\EmailUtils;

final class UserOptions
{
    /** @var bool */
    private $addToNewsletterList = false;
    /** @var string|null */
    private $email;
    /** @var AnswersInterface */
    private $options;
    /** @var bool */
    private $sendEmail = false;
    /** @var string|null */
    private $username;

    /**
     * @param AnswersInterface $options
     */
    public function __construct( AnswersInterface $options )
    {
        $this->setOptions( $options );
    }

    /**
     * @return bool
     */
    public function getAddToNewsletterList(): bool
    {
        return $this->addToNewsletterList;
    }

    /**
     * @param bool $addToNewsletterList
     *
     * @return UserOptions
     */
    public function setAddToNewsletterList( bool $addToNewsletterList ): UserOptions
    {
        $this->addToNewsletterList = $addToNewsletterList;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return UserOptions
     */
    public function setEmail( ?string $email ): UserOptions
    {
        if ( EmailUtils::isValid( $email ) ) {
            $this->email = $email;
        }

        return $this;
    }

    /**
     * @return AnswersInterface
     */
    public function getOptions(): AnswersInterface
    {
        return $this->options;
    }

    /**
     * @param AnswersInterface $options
     *
     * @return UserOptions
     */
    public function setOptions( AnswersInterface $options ): UserOptions
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSendEmail(): bool
    {
        return $this->sendEmail;
    }

    /**
     * @param bool $sendEmail
     *
     * @return UserOptions
     */
    public function setSendEmail( bool $sendEmail ): UserOptions
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     *
     * @return UserOptions
     */
    public function setUsername( ?string $username ): UserOptions
    {
        $this->username = $username;

        return $this;
    }
}
