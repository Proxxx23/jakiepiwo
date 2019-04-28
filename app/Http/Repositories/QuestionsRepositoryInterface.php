<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface QuestionsRepositoryInterface
{
    /**
     * @param bool $json
     *
     * @return mixed
     */
    public function fetchQuestions( bool $json = false );
}
