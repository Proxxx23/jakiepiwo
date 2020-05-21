<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\Answers;
use App\Http\Objects\FormData;
use PHPUnit\Framework\TestCase;

final class FormDataTest extends TestCase
{
    public function testReturnsEmailForValidEmail(): void
    {
        $answers = $this->createMock( Answers::class );
        $formData = new FormData (
            $answers, [
                'newsletter' => false,
                'resultsHash' => 'abcabc',
                'admin' => true,
                'email' => 'valid@email.com',
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
                'resultsHash' => 'abcabc',
                'admin' => true,
                'username' => 'mock',
            ]
        );

        self::assertNull( $formData->getEmail() );
    }
}
