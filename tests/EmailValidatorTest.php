<?php
declare( strict_types=1 );

use App\Http\Utils\Email;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    public function testReturnsTrueForEmptyEmail(): void
    {
        self::assertTrue( Email::isValid( '' ) );
    }

    public function testReturnsTrueForNullEmail(): void
    {
        self::assertTrue( Email::isValid( null ) );
    }

    public function testReturnsTrueForValidEmail(): void
    {
        self::assertTrue( Email::isValid( 'test@email.com' ) );
    }

    /**
     * @dataProvider providerInvalidEmails
     *
     * @param string $invalidEmail
     */
    public function testReturnsTrueForInvalidEmail( string $invalidEmail ): void
    {
        self::assertFalse( Email::isValid( $invalidEmail ) );
    }

    /**
     * @return array
     */
    public function providerInvalidEmails(): array
    {
        return [
            ['0'],
            ['invalid.email'],
            ['@email.email.email.com'],
        ];
    }
}
