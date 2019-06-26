# Firestore Cache Driver for Laravel / Firevel

Cache driver for Laravel/Firevel applications running inside App Engine standard environment.

# Installation

`composer require firevel/firestore-cache-driver`

Add firestore driver to config/cache.php
```
    'stores' => [
    	...
        'firestore' => [
            'driver' => 'firestore',
            'collection' => 'cache', // Firestore collection name.
        ],
        ...
   ];
```

Set `CACHE_DRIVER=firestore` in your .env.

# Important Notice

Driver was developed basing on Laravel 5.8+ where TTL is counted in seconds not minutes.

# Limitations

- Up-to-date limitation https://cloud.google.com/firestore/quotas.
- Maximum size for a document	is 1MB, so keep your cache values small.
- Maximum write rate to a document is 1 per second (looks like its higher in practice). It might affect functionalities based on fast value increase (for example api throttling).
- Firestore can delete one document per call, so operations like `flush()` taking some time.
