<?php

declare(strict_types=1);

namespace Tests\Firevel\FirestoreCacheDriver\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Tests\Firevel\FirestoreCacheDriver\Fixtures\TestPerson;
use Tests\Firevel\FirestoreCacheDriver\TestCase;

class FirestoreCacheTest extends TestCase
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     * @dataProvider provideTestData
     */
    public function testAssignment(string $key, $value): void
    {
        // Make sure missing
        $this->assertNull(Cache::get($key));

        // Save some data
        Cache::put($key, $value);

        // Validate it was created equally
        $this->assertFirestoreKeyExists($key);
        $this->assertFirestoreKeyEquals($value, $key);

        // Forget it
        Cache::forget($key);
        $this->assertFirestoreKeyMissing($key);
    }

    /**
     * Check if the caches are pruned when expired keys are pulled
     * @return void
     * @dataProvider provideTestData
     */
    public function testExpiry(string $key, $value): void
    {
        // Create an entry on Dec 1st
        Date::setTestNow('2020-12-01T00:00:00+00:00');
        Cache::put($key, $value, now()->addDays(7));

        // Validate it was created
        $this->assertFirestoreKeyExists($key);
        $this->assertFirestoreKeyEquals($value, $key);

        // Access the entry on dec 5th
        Date::setTestNow('2020-12-05T00:00:00+00:00');
        $this->assertEquals($value, Cache::get($key));

        // Access the entry on dec 8th
        Date::setTestNow('2020-12-08T00:00:01+00:00');
        $this->assertNull(Cache::get($key));

        // Validate it was dropped
        $this->assertFirestoreKeyMissing($key);
    }

    /**
     * Check if the keys are removed when expired keys are pulled
     * @return void
     * @dataProvider provideTestData
     */
    public function testPullMethod(string $key, $value): void
    {
        // Create an entry on Dec 1st
        Cache::put($key, $value);

        // Validate it was created
        $this->assertFirestoreKeyExists($key);
        $this->assertFirestoreKeyEquals($value, $key);

        // Pull it and ensure it's removed
        $this->assertEquals($value, Cache::pull($key));
        $this->assertFirestoreKeyMissing($key);
    }

    /**
     * Checks increments and decrements
     * @return void
     */
    public function testIncrementAndDecrement(): void
    {
        $key = 'increase.me';
        $this->assertFirestoreKeyMissing($key);

        Cache::increment($key);
        $this->assertFirestoreKeyEquals(1, $key);

        Cache::increment($key, 42);
        $this->assertFirestoreKeyEquals(1 + 42, $key);

        Cache::decrement($key, 12);
        $this->assertFirestoreKeyEquals(1 + 42 - 12, $key);

        Cache::decrement($key);
        $this->assertFirestoreKeyEquals(1 + 42 - 12 - 1, $key);

        Cache::forget($key);
        Cache::decrement($key);
        $this->assertFirestoreKeyEquals(-1, $key);
    }

    /**
     * Checks increments and decrements
     * @return void
     */
    public function testPutManyAndFlush(): void
    {
        $data = array_combine(
            Arr::pluck($this->provideTestData(), '0'),
            Arr::pluck($this->provideTestData(), '1')
        );

        // Store
        Cache::putMany($data);
        Cache::put('testputandflus', now());

        // Check
        foreach ($data as $key => $value) {
            $this->assertFirestoreKeyEquals($value, $key);
        }

        // Flush
        Cache::flush();

        // Check
        foreach (array_keys($data) as $key) {
            $this->assertFirestoreKeyMissing($key);
        }
    }

    /**
     * Just provide messy data
     * @return array
     */
    public function provideTestData(): array
    {
        return [
            'string' => ['test', 'yellow'],
            'array' => ['i am a douche', ['douche' => true]],
            'object' => ['new-users', new TestPerson('harry', 28)],
            'emoji' => ['data.😎', '🎆'],
            'empty' => ['steve', null]
        ];
    }
}
