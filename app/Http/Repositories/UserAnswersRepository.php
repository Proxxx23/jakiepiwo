<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\FormInput;
use Illuminate\Support\Facades\DB;

final class UserAnswersRepository implements UserAnswersRepositoryInterface
{
    /**
     * @param FormInput $formInput
     * @param array $answers
     */
    public function add( FormInput $formInput, array $answers )
    {
        DB::insert(
            'INSERT INTO `user_answers` 
                                    (`name`, 
                                     `e_mail`, 
                                     `newsletter`, 
                                     `answers`, 
                                     `created_at`) 
                                        VALUES 
                                        (?, ?, ?, ?, ?)',
            [
                $formInput->getUsername(),
                $formInput->getEmail(),
                $formInput->getAddToNewsletterList(),
                \json_encode( $answers, JSON_UNESCAPED_UNICODE ),
                now(),
            ]
        );
    }
}
