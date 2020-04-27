<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\Answers;
use App\Http\Objects\PolskiKraftDataCollection;

interface PolskiKraftRepositoryInterface
{
    public function fetchByStyleId( int $styleId ): ?PolskiKraftDataCollection;
    public function setUserAnswers( Answers $answers ): self;
}
