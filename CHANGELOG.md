# Changelog

- All Notable changes to `uri-manipulations` will be documented in this file


## 1.1.0 - TBD

### Added

The following functions are added to ease URI manipulations

- `Uri\add_basepath`
- `Uri\add_leading_slash`
- `Uri\add_root_label`
- `Uri\add_trailing_slash`
- `Uri\append_host`
- `Uri\append_path`
- `Uri\append_query`
- `Uri\host_to_ascii`
- `Uri\host_to_unicode`
- `Uri\is_absolute`
- `Uri\is_absolute_path`
- `Uri\is_network_path`
- `Uri\is_relative_path`
- `Uri\is_same_document`
- `Uri\merge_query`
- `Uri\normalize`
- `Uri\parse_query`
- `Uri\path_to_ascii`
- `Uri\path_to_binary`
- `Uri\prepend_host`
- `Uri\prepend_path`
- `Uri\relativize`
- `Uri\remove_basepath`
- `Uri\remove_dot_segments`
- `Uri\remove_empty_segments`
- `Uri\remove_labels`
- `Uri\remove_leading_slash`
- `Uri\remove_query_values`
- `Uri\remove_root_label`
- `Uri\remove_trailing_slash`
- `Uri\remove_segments`
- `Uri\remove_zone_id`
- `Uri\replace_basename`
- `Uri\replace_data_uri_parameters`
- `Uri\replace_dirname`
- `Uri\replace_extension`
- `Uri\replace_label`
- `Uri\replace_registrabledomain`
- `Uri\replace_segment`
- `Uri\replace_subdomain`
- `Uri\resolve`
- `Uri\sort_query_keys`

`Uri\Modifier\Formatter` class has a new `format` method which is a alias of `__invoke` to ease usage.

### Fixed

- None

### Deprecated

- None

### Removed

- None

## 1.0.1 - 2017-02-6

### Added

- None

### Fixed

- None

### Deprecated

-  `__invoke` calls on all middlewares

### Removed

- None

## 1.0.0 - 2017-01-17

### Added

- `League\Uri\Modifier\UriMiddlewareInterface`
- `League\Uri\Modifier\Exception`
- `League\Uri\Modifier\CallableAdapter`
- `League\Uri\Modifier\Basename` uri middleware to update the URI dirname path
- `League\Uri\Modifier\Dirname` uri middleware to update the URI basename path
- `League\Uri\Modifier\AddRootLabel` uri middleware to update the URI dirname path
- `League\Uri\Modifier\RemoveRootLabel` uri middleware to update the URI basename path
- `League\Uri\Modifier\AddBasePath` uri middleware to add a base path to your URI
- `League\Uri\Modifier\RemoveBasePath` uri middleware to remove a base path from your URI
- `League\Uri\Modifier\Subdomain` uri middleware to update the URI host subdomains
- `League\Uri\Modifier\RegisterableDomain` uri middleware to update the URI host registerable domains part
- `League\Uri\Modifier\AppendQuery` uri middleware to append data to the URI query component

### Fixed

- bug fix `League\Uri\Modifier\Formatter` for [issue #91](https://github.com/thephpleague/uri/issues/91)

### Deprecated

- None

### Removed

- PHP5 support

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
