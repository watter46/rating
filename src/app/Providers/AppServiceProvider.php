<?php

namespace App\Providers;

use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\Infrastructure\ApiFootball\MockApiFootballRepository;
use App\Infrastructure\FlashLiveSports\MockFlashLiveSportsRepository;
use App\UseCases\Admin\FlashLiveSportsRepositoryInterface;
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
        FlashLiveSportsRepositoryInterface::class => MockFlashLiveSportsRepository::class
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
        Collection::macro('toCollection', function () {
            $wrapCollectionRecursive = function ($items) use (&$wrapCollectionRecursive) {
                if (is_array($items)) {
                    return collect($items)
                        ->map(function ($item) use ($wrapCollectionRecursive) {
                            if (is_array($item)) {
                                return $wrapCollectionRecursive($item);
                            }
        
                            return $item;
                        });
                }
                
                return collect($items);
            };
        
            return $wrapCollectionRecursive($this->toArray());
        });
        
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
