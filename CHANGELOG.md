# Changelog

All Notable changes to `uri-manipulations` will be documented in this file

## Next

### Added

- `League\Uri\Modifier\Basename` uri middleware to update the URI dirname path
- `League\Uri\Modifier\Dirname` uri middleware to update the URI basename path
- `League\Uri\Modifier\AddRootLabel` uri middleware to update the URI dirname path
- `League\Uri\Modifier\RemoveRootLabel` uri middleware to update the URI basename path
- `League\Uri\Modifier\AddBasePath` uri middleware to add a base path to your URI
- `League\Uri\Modifier\RemoveBasePath` uri middleware to remove a base path from your URI
- `League\Uri\Modifier\Subdomain` uri middleware to update the URI host subdomains
- `League\Uri\Modifier\RegisterableDomain` uri middleware to update the URI host registerable domains part

### Fixed

- bug fix `League\Uri\Modifier\Formatter` for [issue #91](https://github.com/thephpleague/uri/issues/91)

### Deprecated

- None

### Removed

- None

## 0.2.0 - 2016-12-09

### Added

- None

### Fixed

- Updated dependencies to `League\Uri\Components`
- bug fix to path modifiers see [issue #91](https://github.com/thephpleague/uri/issues/91)

### Deprecated

- None

### Removed

## 0.1.0 - 2016-12-01

### Added

- `League\Uri\Modifier\Formatter::setEncoding`

### Fixed

- Moved `League\Uri\Formatter` to `League\Uri\Modifier\Formatter`

### Deprecated

- None

### Removed

- `League\Uri\Formatter::getQueryEncoding`
- `League\Uri\Formatter::setQueryEncoding`
- `League\Uri\Formatter::setHostEncoding`
- `League\Uri\Formatter::getHostEncoding`
- `League\Uri\Formatter::getQuerySeparator`
- `League\Uri\Formatter::format`
- `League\Uri\Modifiers\Filters\Flag::withFlags`
- `League\Uri\Modifiers\Filters\ForCallbable::withCallable`
- `League\Uri\Modifiers\Filters\ForCallbable::withCallable`
- `League\Uri\Modifiers\Filters\Keys::withKeys`
- `League\Uri\Modifiers\Filters\Label::withLabel`
- `League\Uri\Modifiers\Filters\Offset::withOffset`
- `League\Uri\Modifiers\Filters\QueryString::withQuery`
- `League\Uri\Modifiers\Filters\Segment::withSegment`
- `League\Uri\Modifiers\Filters\Uri::withUri`
- `League\Uri\Modifiers\DataUriParameters\withParameters`
- `League\Uri\Modifiers\Extension\withExtension`
- `League\Uri\Modifiers\KsortQuery\withAlgorithm`
- `League\Uri\Modifiers\Typecode`
