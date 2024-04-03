<?php

namespace App\Providers;

use App\Infrastructure\ApiFootball\ApiFootballRepository;
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
        SofaScoreRepositoryInterface::class => MockSofaScoreRepository::class
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
