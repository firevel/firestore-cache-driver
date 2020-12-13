<?php

declare(strict_types=1);

namespace Tests\Firevel\FirestoreCacheDriver;

use Firevel\FirestoreCacheDriver\FirestoreCacheServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Tests\Firevel\FirestoreCacheDriver\Fixtures\TestsFirestore;

class TestCase extends OrchestraTestCase
{
    use TestsFirestore;

    /**
     * Flush cache on each startup
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->flushFirestore();
    }

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

    /**
     * Helper to set the cache collection and prefix
     * @param string $collection
     * @param null|string $prefix
     * @return void
     */
    public function setCollectionAndPrefix(string $collection, ?string $prefix): void
    {
        // Update
        $this->app['config']->set('cache.firestore-test.collection', $collection);
        $this->app['config']->set('cache.prefix', $prefix);
    }
}
