<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\StylesLogsRepositoryInterface;

final class UserService
{
    private StylesLogsRepositoryInterface $stylesLogsRepository;

    public function __construct( StylesLogsRepositoryInterface $stylesLogsRepository )
    {
        $this->stylesLogsRepository = $stylesLogsRepository;
    }

    public function getUsername(): ?string
    {
        return $this->stylesLogsRepository->fetchUsernameByIpAddress( $_SERVER['REMOTE_ADDR'] );
    }
}
