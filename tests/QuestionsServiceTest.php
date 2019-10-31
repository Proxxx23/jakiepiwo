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
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
            'tak',
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

    /**
     * @dataProvider providerEmptyAnswers
     *
     * @param array $emptyAnswers
     */
    public function testThrowsOnNonAllTheQuestionsAnswered( array $emptyAnswers ): void
    {
        $answers['answers'] = $emptyAnswers;

        $service = new QuestionsService( new QuestionsRepository() );

        $this->expectException( UnexpectedValueException::class );
        $this->expectExceptionMessage( 'You must answer on all the questions.' );
        $service->validateInput( $answers );
    }

    /**
     * @return array
     */
    public function providerEmptyAnswers(): array
    {
        return [
            'nulls' => [
                [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                ],
            ],
            'empty strings' => [
                [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ],
            ],
            'boolean' => [
                [
                    true,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                ],
            ],
        ];
    }
}
