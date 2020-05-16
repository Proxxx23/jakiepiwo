<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\BeerData;
use App\Http\Objects\FormData;
use Illuminate\Support\Facades\DB;

final class UserAnswersRepository implements UserAnswersRepositoryInterface
{
    public function addAnswers( FormData $formInput, array $answers, BeerData $results ): void
    {
        try {
            DB::table( 'user_answers' )
                ->insert(
                    [
                        'name' => $formInput->getUsername(),
                        'e_mail' => $formInput->getEmail(),
                        'newsletter' => $formInput->addToNewsletterList(),
                        'answers' => \json_encode(
                            $answers, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE
                        ),
                        'results' => \json_encode(
                            $results->toArray(), \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE
                        ),
                        'results_hash' => $formInput->getResultsHash(),
                        'created_at' => \now(),

                    ]
                );
        } catch ( \Exception $ex ) {

        }
    }
}
