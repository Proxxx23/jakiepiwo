<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Http\Repositories\QuestionsRepository;
use App\Http\Services\QuestionsService;

class ValidationUtils
{
    /**
     * @param string|null $email
     *
     * @return bool
     */
    public static function validateEmail( ?string $email ): bool
    {
        $email = \filter_var( \trim( $email ), \FILTER_SANITIZE_EMAIL );

        return !\preg_match(
            '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4]\d)|(?:1\d{2})|(?:[1-9]?\d))(?:\.(?:(?:25[0-5])|(?:2[0-4]\d)|(?:1\d{2})|(?:[1-9]?\d))){3}))\]))$/iD',
            $email
        );
    }

    /**
     * Validates yes/no answers
     *
     * @param string $answer
     *
     * @return bool
     */
    public static function validateSimpleAnswer( string $answer ): bool
    {
        return ( \strtolower( $answer ) !== 'tak' && \strtolower( $answer ) !== 'nie' );
    }

    /**
     * Validates "scale" answers
     *
     * @param string $answer
     *
     * @return bool
     */
    public static function validateAnswers( string $answer ): bool
    {
        $questionsService = new QuestionsService( new QuestionsRepository() );
        $questions = $questionsService->getQuestions();

        return !\in_array( $answer, $questions[6]['answers'], true ) ||
            !\in_array( $answer, $questions[8]['answers'], true );
    }
}
