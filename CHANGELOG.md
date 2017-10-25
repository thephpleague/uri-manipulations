# Changelog

- All Notable changes to `uri-manipulations` will be documented in this file

## 1.1.0 - 2017-10-25

### Added

The following functions are added to ease URI manipulations

- `League\Uri\add_basepath`
- `League\Uri\add_leading_slash`
- `League\Uri\add_root_label`
- `League\Uri\add_trailing_slash`
- `League\Uri\append_host`
- `League\Uri\append_query`
- `League\Uri\append_path`
- `League\Uri\host_to_ascii`
- `League\Uri\host_to_unicode`
- `League\Uri\is_absolute`
- `League\Uri\is_absolute_path`
- `League\Uri\is_network_path`
- `League\Uri\is_relative_path`
- `League\Uri\is_same_document`
- `League\Uri\merge_query`
- `League\Uri\normalize`
- `League\Uri\path_to_ascii`
- `League\Uri\path_to_binary`
- `League\Uri\prepend_host`
- `League\Uri\prepend_path`
- `League\Uri\relativize`
- `League\Uri\remove_basepath`
- `League\Uri\remove_dot_segments`
- `League\Uri\remove_empty_segments`
- `League\Uri\remove_labels`
- `League\Uri\remove_leading_slash`
- `League\Uri\remove_pairs`
- `League\Uri\remove_root_label`
- `League\Uri\remove_trailing_slash`
- `League\Uri\remove_segments`
- `League\Uri\remove_zone_id`
- `League\Uri\replace_basename`
- `League\Uri\replace_data_uri_parameters`
- `League\Uri\replace_dirname`
- `League\Uri\replace_extension`
- `League\Uri\replace_label`
- `League\Uri\replace_registrabledomain`
- `League\Uri\replace_segment`
- `League\Uri\replace_subdomain`
- `League\Uri\resolve`
- `League\Uri\sort_query`
- `League\Uri\uri_to_rfc3986`
- `League\Uri\uri_to_rfc3987`

`League\Uri\Modifiers\Formatter` class has a new `format` method which is a alias of `__invoke` to ease usage.

### Fixed

- None

### Deprecated

- `League\Uri\Modifiers\uri_reference` function is deprecated you should update your code to use one of the following functions:
    - `League\Uri\is_absolute`
    - `League\Uri\is_absolute_path`
    - `League\Uri\is_network_path`
    - `League\Uri\is_relative_path`
    - `League\Uri\is_same_document`

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

- `League\Uri\Modifiers\UriMiddlewareInterface`
- `League\Uri\Modifiers\Exception`
- `League\Uri\Modifiers\CallableAdapter`
- `League\Uri\Modifiers\Basename` uri middleware to update the URI dirname path
- `League\Uri\Modifiers\Dirname` uri middleware to update the URI basename path
- `League\Uri\Modifiers\AddRootLabel` uri middleware to update the URI dirname path
- `League\Uri\Modifiers\RemoveRootLabel` uri middleware to update the URI basename path
- `League\Uri\Modifiers\AddBasePath` uri middleware to add a base path to your URI
- `League\Uri\Modifiers\RemoveBasePath` uri middleware to remove a base path from your URI
- `League\Uri\Modifiers\Subdomain` uri middleware to update the URI host subdomains
- `League\Uri\Modifiers\RegisterableDomain` uri middleware to update the URI host registerable domains part
- `League\Uri\Modifiers\AppendQuery` uri middleware to append data to the URI query component

### Fixed

- bug fix `League\Uri\Modifiers\Formatter` for [issue #91](https://github.com/thephpleague/uri/issues/91)

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

- `League\Uri\Modifiers\Formatter::setEncoding`

### Fixed

- Moved `League\Uri\Formatter` to `League\Uri\Modifiers\Formatter`

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
