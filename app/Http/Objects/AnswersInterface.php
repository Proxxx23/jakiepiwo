<?php
declare( strict_types=1 );

namespace App\Http\Objects;

interface AnswersInterface
{
    public function fetchAll(): void;
}
