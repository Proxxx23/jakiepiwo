<?php
declare( strict_types=1 );

use App\Http\Repositories\QuestionsRepository;
use App\Http\Services\QuestionsService;
use PHPUnit\Framework\TestCase;

class QuestionsServiceTest extends TestCase
{
    /**
     * @group integration
     */
    public function testReturnsGivenAnswersAfterValidation(): void
    {
        $answers['answers'] = [
            0 => 'tak',
            1 => 'tak',
            2 => 'tak',
            3 => 'tak',
            4 => 'tak',
            5 => 'tak',
            6 => 'tak',
            7 => 'tak',
            8 => 'tak',
            9 => 'tak',
            10 => 'tak',
            11 => 'tak',
            12 => 'tak',
            13 => 'tak',
            14 => 'tak',
        ];

        $service = new QuestionsService( new QuestionsRepository() );

        self::assertEquals( $answers['answers'], $service->validateInput( $answers ) );
    }

    /**
     * @group integration
     */
    public function testThrowsOnInvalidAnswersCount(): void
    {
        $answers['answers'] = [
            0 => 'tak',
            1 => 'tak',
            2 => 'tak',
            3 => 'tak',
            4 => 'tak',
            5 => 'tak',
        ];

        $service = new QuestionsService( new QuestionsRepository() );

        $this->expectException( UnexpectedValueException::class );
        $this->expectExceptionMessage( 'Number of answers do not match number of questions.' );
        $service->validateInput( $answers );
    }
}
