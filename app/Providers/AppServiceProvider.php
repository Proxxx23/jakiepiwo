<?php

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
use App\Http\Services\AlgorithmService;
use App\Http\Services\AnswersLoggerService;
use App\Http\Services\NewsletterService;
use App\Http\Services\OnTapService;
use App\Http\Services\QuestionsService;
use App\Http\Services\SimpleResultsService;
use App\Http\Utils\Dictionary;
use App\Http\Utils\ErrorsLogger;
use App\Http\Utils\SharedCache;
use DrewM\MailChimp\MailChimp;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class AppServiceProvider extends ServiceProvider
{
    private const DEFAULT_ONTAP_TIMEOUT = 10; // in seconds
    private const DEFAULT_GEOLOCATION_TIMEOUT = 3; // in seconds

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ErrorsLogger::class, static function () {
            return new ErrorsLogger( new ErrorLogsRepository() );
        }
        );
        $this->app->singleton(
            'ScoringRepository', static function () {
            return new ScoringRepository();
        }
        );
        $this->app->singleton(
            'StylesLogsRepository', static function () {
            return new StylesLogsRepository();
        }
        );
        $this->app->singleton(
            'BeersRepository', static function () {
            return new BeersRepository();
        }
        );
        $this->app->singleton(
            'SharedCacheFilesystemAdapter', static function () {
            return new FilesystemAdapter( '', SharedCache::DEFAULT_CACHE_TTL );
        }
        );
        $this->app->singleton(
            'SharedCache', static function () {
            return new SharedCache( \resolve( 'SharedCacheFilesystemAdapter' ) );
        }
        );
        $this->app->singleton(
            'HttpClient', static function () {
            return new Client();
        }
        );
        $this->app->singleton(
            'UntappdRepository', static function () {
            return new UntappdRepository(
                \resolve( 'HttpClient' ),
                \resolve( 'SharedCache' ),
            );
        }
        );
        $this->app->singleton(
            'PolskiKraftRepository', static function () {
            return new PolskiKraftRepository(
                new Dictionary(),
                \resolve( 'SharedCache' ),
                \resolve( 'HttpClient' ),
                \resolve( 'UntappdRepository' )
            );
        }
        );
        $this->app->singleton(
            'AlgorithmService', static function () {
            return new AlgorithmService(
                \resolve( 'ScoringRepository' ),
                \resolve( 'PolskiKraftRepository' ),
                \resolve( 'StylesLogsRepository' ),
                \resolve( 'BeersRepository' ),
                \resolve( ErrorsLogger::class )
            );
        }
        );
        $this->app->singleton(
            'NewsletterService', static function () {
            return new NewsletterService(
                new NewsletterRepository( new MailChimp( \config( 'mail.mailchimpApiKey' ) ) )
            );
        }
        );
        $this->app->singleton(
            'AnswersLoggerService', static function () {
            return new AnswersLoggerService( new UserAnswersRepository() );
        }
        );
        $this->app->singleton(
            'SimpleResultsService', static function () {
            return new SimpleResultsService( new ResultsRepository(), \resolve( 'SharedCache' ) );
        }
        );
        $this->app->singleton(
            'QuestionsService', static function () {
            return new QuestionsService( new QuestionsRepository() );
        }
        );
        $this->app->singleton(
            'OnTapService', static function () {

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
