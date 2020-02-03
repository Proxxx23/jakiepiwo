<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\FormData;
use Illuminate\Support\Facades\DB;

final class UserAnswersRepository implements UserAnswersRepositoryInterface
{
    public function addAnswers( FormData $formInput, array $answers, array $results ): void
    {
        $now = \now();
        $hashKey = \str_replace( ' ', '', \implode( '', $results['answers'] ) ) . $now;
        $resultsHash = \hash('ripemd128', $hashKey);

        try {
            DB::insert(
                'INSERT INTO `user_answers` 
                                    (`name`, 
                                     `e_mail`, 
                                     `newsletter`, 
                                     `answers`,
                                     `results`,
                                     `results_hash`,
                                     `created_at`) 
                                        VALUES 
                                        (?, ?, ?, ?, ?, ?, ?)',
                [
                    $formInput->getUsername(),
                    $formInput->getEmail(),
                    $formInput->addToNewsletterList(),
                    \json_encode( $answers, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE, 512 ),
                    \json_encode( $results, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE, 512 ),
                    $resultsHash,
                    $now,
                ]
            );
        } catch ( \Exception $exception ) {

        }
    }
}
