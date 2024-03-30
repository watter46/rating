<?php

namespace App\Providers;

use App\Infrastructure\ApiFootball\ApiFootballRepository;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * 登録する必要のある全コンテナ結合
     *
     * @var array
     */
    public $bindings = [
        ApiFootballRepositoryInterface::class => ApiFootballRepository::class,
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
