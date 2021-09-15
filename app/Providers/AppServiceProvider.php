<?php
declare( strict_types=1 );

namespace App\Providers;

use App\Http\Repositories\BeersRepository;
use App\Http\Repositories\ErrorLogsRepository;
use App\Http\Repositories\GeolocationRepository;
use App\Http\Repositories\NewsletterRepository;
use App\Http\Repositories\OnTapRepository;
use App\Http\Repositories\PolskiKraftRepository;
use App\Http\Repositories\QuestionsRepository;
use App\Http\Repositories\ResultsRepository;
use App\Http\Repositories\ScoringRepository;
use App\Http\Repositories\StylesLogsRepository;
use App\Http\Repositories\UntappdRepository;
use App\Http\Repositories\UserAnswersRepository;
use App\Services\AlgorithmService;
use App\Services\AnswersLoggerService;
use App\Services\NewsletterService;
use App\Services\OnTapService;
use App\Services\QuestionsService;
use App\Services\SimpleResultsService;
use App\Services\UntappdService;
use App\Utils\Dictionary;
use App\Utils\ErrorsLogger;
use App\Utils\SharedCache;
use DrewM\MailChimp\MailChimp;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class AppServiceProvider extends ServiceProvider
{
    private const DEFAULT_ONTAP_TIMEOUT = 10; // in seconds
    private const DEFAULT_GEOLOCATION_TIMEOUT = 3; // in seconds

    public function register(): void
    {
        $this->app->bind(
            ErrorsLogger::class, static fn(): ErrorsLogger => new ErrorsLogger( new ErrorLogsRepository() )
        );
        $this->app->singleton(
            'ScoringRepository', static fn(): ScoringRepository => new ScoringRepository()
        );
        $this->app->singleton(
            'StylesLogsRepository', static fn(): StylesLogsRepository => new StylesLogsRepository()
        );
        $this->app->singleton(
            'BeersRepository', static fn(): BeersRepository => new BeersRepository()
        );
        $this->app->singleton(
            'SharedCacheFilesystemAdapter',
            static fn(): FilesystemAdapter => new FilesystemAdapter( '', SharedCache::DEFAULT_CACHE_TTL )
        );
        $this->app->singleton(
            'SharedCache', static fn(): SharedCache => new SharedCache( \resolve( 'SharedCacheFilesystemAdapter' ) )
        );
        $this->app->singleton(
            'HttpClient', static fn(): Client => new Client(['verify' => false])
        );
        $this->app->singleton(
            'UntappdRepository', static fn(): UntappdRepository => new UntappdRepository(
            \resolve( 'HttpClient' ),
            \resolve( 'SharedCache' ),
        )
        );
        $this->app->singleton(
            'UntappdService', static fn(): UntappdService => new UntappdService(
            \resolve( 'UntappdRepository' ),
        )
        );
        $this->app->singleton(
            'PolskiKraftRepository', static fn(): PolskiKraftRepository => new PolskiKraftRepository(
            new Dictionary(),
            \resolve( 'SharedCache' ),
            \resolve( 'HttpClient' ),
            \resolve( 'UntappdRepository' )
        )
        );
        $this->app->singleton(
            'AlgorithmService', static fn(): AlgorithmService => new AlgorithmService(
            \resolve( 'ScoringRepository' ),
            \resolve( 'PolskiKraftRepository' ),
            \resolve( 'StylesLogsRepository' ),
            \resolve( 'BeersRepository' ),
            \resolve( ErrorsLogger::class )
        )
        );
        $this->app->singleton(
            'NewsletterService', static fn(): NewsletterService => new NewsletterService(
            new NewsletterRepository( new MailChimp( \config( 'mail.mailchimpApiKey' ) ) )
        )
        );
        $this->app->singleton(
            'AnswersLoggerService',
            static fn(): AnswersLoggerService => new AnswersLoggerService( new UserAnswersRepository() )
        );
        $this->app->singleton(
            'SimpleResultsService',
            static fn() => new SimpleResultsService( new ResultsRepository(), \resolve( 'SharedCache' ) )
        );
        $this->app->singleton(
            'QuestionsService', static fn(): QuestionsService => new QuestionsService( new QuestionsRepository() )
        );
        $this->app->singleton(
            'OnTapService', static function (): OnTapService {

            $onTapConfig = [ 'timeout' => self::DEFAULT_ONTAP_TIMEOUT ];
            $geolocationConfig = [ 'timeout' => self::DEFAULT_GEOLOCATION_TIMEOUT ];

            return new OnTapService(
                new OnTapRepository( new Client( $onTapConfig ), \resolve( 'SharedCache' ) ),
                new GeolocationRepository( new Client( $geolocationConfig ) )
            );
        }
        );
    }
}
