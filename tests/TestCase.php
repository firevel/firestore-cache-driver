<?php

declare(strict_types=1);

namespace Tests\Firevel\FirestoreCacheDriver;

use Firevel\FirestoreCacheDriver\FirestoreCacheServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            FirestoreCacheServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     * @param  \Illuminate\Foundation\Application   $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->make('config');
        $config->set('cache.default', 'firestore-test');
        $config->set('cache.stores.firestore-test', [
            'driver' => 'firestore',
            'collection' => 'testing'
        ]);
    }
}
