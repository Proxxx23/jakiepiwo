<?php
declare( strict_types=1 );

namespace App\Http\Objects;

use App\Http\Utils\Email;

final class FormInput
{
    /** @var bool */
    private $addToNewsletterList = false;
    /** @var AnswersInterface */
    private $answers;
    /** @var string|null */
    private $email;
    /** @var bool */
    private $sendEmail = false;
    /** @var string|null */
    private $username;

    /**
     * @param AnswersInterface $answers
     * @param array $requestData
     */
    public function __construct( AnswersInterface $answers, array $requestData )
    {
        $this->addToNewsletterList = \is_bool( $requestData['newsletter'] ) ? $requestData['newsletter'] : false;
        $this->answers = $answers;
        $this->email = Email::isValid( $requestData['email'] )
            ? $requestData['email']
            : null;
        $this->sendEmail = \is_bool( $requestData['sendEmail'] ) ? $requestData['sendEmail'] : false;
        $this->username = \is_string( $requestData['username'] ) && $requestData['username'] !== ''
            ? $requestData['username']
            : null;
    }

    /**
     * @return bool
     */
    public function getAddToNewsletterList(): bool
    {
        return $this->addToNewsletterList;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return AnswersInterface
     */
    public function getAnswers(): AnswersInterface
    {
        return $this->answers;
    }

    /**
     * @return bool
     */
    public function getSendEmail(): bool
    {
        return $this->sendEmail;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
}
