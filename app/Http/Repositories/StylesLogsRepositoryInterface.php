<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\FormData;

interface StylesLogsRepositoryInterface
{
    /**
     * @param string $ipAddress
     * @return string|null
     */
    public function fetchUsernameByIpAddress( string $ipAddress ): ?string;

    /**
     * @param FormData $user
     * @param array|null $styleToTake
     * @param array|null $styleToAvoid
     */
    public function logStyles( FormData $user, ?array $styleToTake, ?array $styleToAvoid ): void;
}
