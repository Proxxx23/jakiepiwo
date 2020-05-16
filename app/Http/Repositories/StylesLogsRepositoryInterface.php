<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\FormData;

interface StylesLogsRepositoryInterface
{
    public function logStyles( FormData $user, ?array $recommendedStyle, ?array $unsuitableStyle ): void;
}
