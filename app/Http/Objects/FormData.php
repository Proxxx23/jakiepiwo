<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class FormData
{
    private bool $addToNewsletterList;
    private Answers $answers;
    private ?string $email;
    private ?string $username;
    private ?string $resultsHash;

    public function __construct( Answers $answers, array $requestData )
    {
        $this->addToNewsletterList = \is_bool( $requestData['newsletter'] ) ? $requestData['newsletter'] : false;
        $this->answers = $answers;
        $this->email = $this->emailIsValid( $requestData['email'] )
            ? $requestData['email']
            : null;
        $this->username = \is_string( $requestData['username'] ) && $requestData['username'] !== ''
            ? $requestData['username']
            : null;
        $this->resultsHash = \is_string( $requestData['resultsHash'] ) && $requestData['resultsHash'] !== ''
            ? $requestData['resultsHash']
            : null;
    }

    public function addToNewsletterList(): bool
    {
        return $this->addToNewsletterList && !empty( $this->email );
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getAnswers(): Answers
    {
        return $this->answers;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getResultsHash(): ?string
    {
        return $this->resultsHash;
    }

    private function emailIsValid( ?string $email ): bool
    {
        if ( $email === null || $email === '' ) {
            return false;
        }

        $email = \filter_var( \trim( $email ), \FILTER_SANITIZE_EMAIL );
        if ( $email === false) {
            return false;
        }

        return (bool) \preg_match(
            '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4]\d)|(?:1\d{2})|(?:[1-9]?\d))(?:\.(?:(?:25[0-5])|(?:2[0-4]\d)|(?:1\d{2})|(?:[1-9]?\d))){3}))\]))$/iD',
            $email
        );
    }
}
