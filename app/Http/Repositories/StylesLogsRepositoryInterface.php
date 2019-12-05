<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\FormData;

interface StylesLogsRepositoryInterface
{
    public function fetchUsernameByIpAddress( string $ipAddress ): ?string;
    public function logStyles( FormData $user, ?array $styleToTake, ?array $styleToAvoid ): void;
}
