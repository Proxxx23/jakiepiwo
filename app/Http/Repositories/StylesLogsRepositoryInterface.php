<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface StylesLogsRepositoryInterface
{
    public function fetchUsername( string $ipAddress ): ?string;
}
