<?php

namespace Tests;

use App\Http\Objects\AnswersInterface;
use App\Http\Objects\FormData;
use PHPUnit\Framework\TestCase;

class FormDataTest extends TestCase
{
    public function testReturnsEmailForValidEmail(): void
    {
        $mockAnswersInterface = $this->createMock( AnswersInterface::class );
        $formData = new FormData ($mockAnswersInterface, [
            'newsletter' => false,
            'email' => 'valid@email.com',
            'sendEmail' => false,
            'username' => 'mock',
        ] );

        self::assertNotNull( $formData->getEmail() );
    }

    public function testReturnsNullForInvalidEmail(): void
    {
        $mockAnswersInterface = $this->createMock( AnswersInterface::class );
        $formData = new FormData ($mockAnswersInterface, [
            'newsletter' => false,
            'email' => 'invali&@#',
            'sendEmail' => false,
            'username' => 'mock',
        ] );

        self::assertNull( $formData->getEmail() );
    }
}
