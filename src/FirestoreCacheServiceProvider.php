<?php

namespace Firevel\FirestoreCacheDriver;

use Firevel\FirestoreCacheDriver\FirestoreCache;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class FirestoreCacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Cache::extend('firestore', function ($app) {
            $store = $app['config']['cache.default'];
            return Cache::repository(
                new FirestoreCache(
                    new FirestoreClient(),
                    $app['config']["cache.stores.{$store}.collection"],
                    $this->app['config']['cache.prefix']
                )
            );
        });
    }
}
