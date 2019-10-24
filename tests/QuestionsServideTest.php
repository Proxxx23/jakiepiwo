<?php
declare( strict_types=1 );

use App\Http\Repositories\QuestionsRepository;
use App\Http\Services\QuestionsService;
use PHPUnit\Framework\TestCase;

class QuestionsServideTest extends TestCase
{
    /**
     * @group integration
     */
    public function testReturnsGivenAnswersAfterValidation(): void
    {
        $answers['answers'] = [
            0,
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
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
            0,
            1,
            2,
            3,
            4,
            5,
        ];

        $service = new QuestionsService( new QuestionsRepository() );

        $this->expectException( UnexpectedValueException::class );
        $this->expectExceptionMessage( 'Number of answers do not match number of questions.' );
        $service->validateInput( $answers );
    }
}
