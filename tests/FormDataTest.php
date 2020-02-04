<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\Answers;
use App\Http\Objects\FormData;

final class FormDataTest extends FinalsBypassedTestCase
{
    public function testReturnsEmailForValidEmail(): void
    {
        $answers = $this->createMock( Answers::class );
        $formData = new FormData (
            $answers, [
            'newsletter' => false,
            'email' => 'valid@email.com',
            'sendEmail' => false,
            'username' => 'mock',
        ]
        );

        self::assertNotNull( $formData->getEmail() );
    }

    public function testReturnsNullForInvalidEmail(): void
    {
        $answers = $this->createMock( Answers::class );
        $formData = new FormData (
            $answers, [
            'newsletter' => false,
            'email' => 'invali&@#',
            'sendEmail' => false,
            'username' => 'mock',
        ]
        );

        self::assertNull( $formData->getEmail() );
    }
}
