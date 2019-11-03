<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\StylesLogsRepositoryInterface;

final class UserService
{
    /** @var StylesLogsRepositoryInterface */
    private $stylesLogsRepository;

    /**
     * @param StylesLogsRepositoryInterface $stylesLogsRepository
     */
    public function __construct( StylesLogsRepositoryInterface $stylesLogsRepository )
    {
        $this->stylesLogsRepository = $stylesLogsRepository;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->stylesLogsRepository->fetchUsernameByIpAddress( $_SERVER['REMOTE_ADDR'] );
    }
}
