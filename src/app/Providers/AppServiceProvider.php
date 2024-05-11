<?php

namespace App\Providers;

use App\Infrastructure\ApiFootball\ApiFootballRepository;
use App\Infrastructure\SofaScore\SofaScoreRepository;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\SofaScoreRepositoryInterface;
use Database\Stubs\Infrastructure\ApiFootball\MockApiFootballRepository;
use Database\Stubs\Infrastructure\SofaScore\MockSofaScoreRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * 登録する必要のある全コンテナ結合
     *
     * @var array
     */
    public $bindings = [
        // ApiFootballRepositoryInterface::class => ApiFootballRepository::class,
        ApiFootballRepositoryInterface::class => MockApiFootballRepository::class,
        SofaScoreRepositoryInterface::class => SofaScoreRepository::class
        // SofaScoreRepositoryInterface::class => MockSofaScoreRepository::class
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Collection::macro('dataGet', function ($key, bool $collection = true) {
            $data = $this->toArray();
            
            return $collection
                ? collect(data_get($data, $key))
                : data_get($data, $key);
        });

        Collection::macro('dataSet', function ($key, $value) {
            $data = $this->toArray();
            
            return collect(data_set($data, $key, $value));
        });

        Collection::macro('fromStd', function () {
            return collect(json_decode($this, true));
        });
    }
}
