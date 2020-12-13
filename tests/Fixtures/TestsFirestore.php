<?php

declare(strict_types=1);

namespace Tests\Firevel\FirestoreCacheDriver\Fixtures;

use Google\Cloud\Firestore\DocumentSnapshot;
use Google\Cloud\Firestore\FirestoreClient;
use PHPUnit\Framework\TestCase as PHPUnit;

trait TestsFirestore
{

    /**
     * Test if a given key exists in the Firestore
     * @param string $key
     * @param null|string $collection
     * @param null|string $prefix
     * @return void
     */
    protected function assertFirestoreKeyExists(string $key, ?string $collection = null, ?string $prefix = null): void
    {
        PHPUnit::assertTrue(
            $this->firestoreSnapshot($key, $collection, $prefix)->exists(),
            "The cache key [[$prefix]-[$key]] could not be found in [[$collection]]"
        );
    }

    /**
     * Test if a given key does not exist in the Firestore
     * @param string $key
     * @param null|string $collection
     * @param null|string $prefix
     * @return void
     */
    protected function assertFirestoreKeyMissing(string $key, ?string $collection = null, ?string $prefix = null): void
    {
        PHPUnit::assertFalse(
            $this->firestoreSnapshot($key, $collection, $prefix)->exists(),
            "The cache key [[$prefix]-[$key]] could not be found in [[$collection]]"
        );
    }

    /**
     * Checks if a cache value matches the given value
     * @param mixed $expected
     * @param string $key
     * @param null|string $collection
     * @param null|string $prefix
     * @return void
     */
    protected function assertFirestoreKeyEquals($expected, string $key, ?string $collection = null, ?string $prefix = null): void
    {
        $data = $this->firestoreSnapshot($key, $collection, $prefix)->data();
        $data = $data ? unserialize($data['value']) : null;

        PHPUnit::assertEquals(
            $expected,
            $data,
            "The cache key [[$prefix]-[$key]] in [{$collection}] does not match the value given"
        );
    }

    /**
     * @return null|string
     */
    protected function getConfiguredCollection(): ?string
    {
        $store = $this->app['config']->get('cache.default');
        return (string) $this->app['config']->get("cache.stores.{$store}.collection");
    }

    /**
     * @return null|string
     */
    protected function getConfiguredPrefix(): ?string
    {
        return (string) $this->app['config']->get('cache.prefix', '');
    }

    /**
     * @return FirestoreClient
     */
    private function getFirestore(): FirestoreClient
    {
        static $client = null;
        return $client ?? ($client = new FirestoreClient());
    }

    /**
     * @return void
     */
    protected function flushFirestore(): void
    {
        $this->afterApplicationCreated(function () {
            $collection = $this->getConfiguredCollection();

            if (!$collection) {
                return;
            }

            foreach ($this->getFirestore()->collection($collection)->documents() as $item) {
                $item->reference()->delete();
            }
        });
    }

    /**
     * Checks if the number of documents matches the expected count
     * @param mixed $expected
     * @param string $key
     * @param null|string $collection
     * @param null|string $prefix
     * @return void
     */
    protected function assertFirestoreCount($expected, ?string $collection = null, ?string $prefix = null): void
    {
        PHPUnit::assertSame(
            $expected,
            $this->firestoreCount($collection, $prefix),
            "Expected firestore to contain {$expected} documents"
        );
    }

    /**
     * Gets a read-only connection to the given document
     * @param string $key
     * @param null|string $collection
     * @param null|string $prefix
     * @return DocumentSnapshot
     */
    private function firestoreSnapshot(
        string $key,
        ?string $collection = null,
        ?string $prefix = null
    ): DocumentSnapshot {
        $collection = $collection ?? $this->getConfiguredCollection();
        $prefix = $prefix ?? $this->getConfiguredPrefix();

        return $this->getFirestore()
            ->collection($collection)
            ->document($prefix . $key)
            ->snapshot();
    }

    /**
     * Returns the number of files matching the given query
     * @param null|string $collection
     * @param null|string $prefix
     * @return int
     */
    private function firestoreCount(?string $collection = null, ?string $prefix = null): int
    {
        $collection = $collection ?? $this->getConfiguredCollection();
        $prefix = $prefix ?? $this->getConfiguredPrefix();

        return $this->getFirestore()
            ->collection($collection)
            ->where('prefix', '=', $prefix)
            ->documents()
            ->size();
    }

    /**
     * Dumps the current store contents
     * @return void
     */
    protected function dumpStore(): void
    {
        $res = [];
        foreach ($this->getFirestore()->collection($this->getConfiguredCollection())->documents() as $item) {
            $key = $item->id();
            $data = $item->data();

            $res[$key] = $data;
        }

        dump($res);
    }
}
