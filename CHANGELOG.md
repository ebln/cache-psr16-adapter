Changelog
=========

Intended to follow [«Keep a Changelog»](https://keepachangelog.com/en/)

----

## [Unreleased] (meant as staging area)

### Changed

- Using `NowFactory` also for `CacheItem`

### Added

- Increase test coverage (using PSR-16 exceptions)
- Add tests to ensure PSR-6 commits make use of PSR-16's bulk setters

### TODO

- Refactor `NowFactory` to PSR-20 [BREAKING]
- Revisit `\Brnc\CachePsr16Adapter\CacheItemPool::getTimeToLive`
- Change `getItems` to use `getMultiple`

----

## [1.0.0] - 2021-10-10

### Added

- PSR-6 caching instance adapting an existing PSR-16 cache

----

## [0.0.0] - 1970-01-01 Template

### Added

- Feature A

### Changed

### Deprecated

### Removed

### Fixed

### Security
