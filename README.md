PSR-16 to PSR-6 Adapter
=======================

This package provides a PSR-6 cache instance when you only have a PSR-16 cache at hand.
As PSR-6 is more feature-rich than PSR-16, this adaption is not utterly performant. And you should use it very carefully.

A suitable use-case might be that you already went with the leaner PSR-16 in your project but now want to add a third-party package that only supports PSR-6. It should be fine if that package uses the cache only at initialization, e.g. for schema caching. If, however, there is excessive or highly interactive caching traffic, you should consider refactoring your project towards PSR-6.

##Usage
```php
    $psr16 = new \Psr\SimpleCache\CacheInterface();
    $psr6  = new \Brnc\CachePsr16Adapter\CacheItemPool($psr16);
```

The constructor takes an optional second argument for a `NowFactory` to enable testing and mocking.
Once PSR-20 (Clock) is accepted, the second argument and the `NowFactory` get refactored towards this interface!
