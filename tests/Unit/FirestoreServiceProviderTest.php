<?php

declare(strict_types=1);

namespace Tests\Firevel\FirestoreCacheDriver\Unit;

use Firevel\FirestoreCacheDriver\FirestoreCache;
use Illuminate\Support\Facades\Cache;
use Tests\Firevel\FirestoreCacheDriver\TestCase;

class FirestoreServiceProviderTest extends TestCase
{
    public function testStoreDriver(): void
    {
        $this->setCollectionAndPrefix('my-collection', 'my-prefix');

        /** @var FirestoreCache $store */
        $store = Cache::store('firestore-test')->getStore();

        $this->assertInstanceOf(FirestoreCache::class, $store);

        $this->assertSame('my-prefix', $store->getPrefix());
    }
}
