<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League.uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @copyright  2017 Ignace Nyamagana Butera
 * @license    https://github.com/thephpleague/uri-manipulations/blob/master/LICENSE (MIT License)
 * @version    1.5.0
 * @link       https://github.com/thephpleague/uri-manipulations
 */
declare(strict_types=1);

namespace League\Uri;

use League\Uri\Interfaces\Uri as LeagueUriInterface;
use League\Uri\PublicSuffix\Rules;
use Psr\Http\Message\UriInterface;

/**
 * Add a new basepath to the URI path
 *
 * @see Modifiers\AddBasePath::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $path
 *
 * @return LeagueUriInterface|UriInterface
 */
function add_basepath($uri, string $path)
{
    return (new Modifiers\AddBasePath($path))->process($uri);
}

/**
 * Add a leading slash to the URI path
 *
 * @see Modifiers\AddLeadingSlash::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function add_leading_slash($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\AddLeadingSlash();

    return $modifier->process($uri);
}

/**
 * Add the root label to the URI
 *
 * @see Modifiers\AddRootLabel::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function add_root_label($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\AddRootLabel();

    return $modifier->process($uri);
}

/**
 * Add a trailing slash to the URI path
 *
 * @see Modifiers\AddTrailingSlash::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function add_trailing_slash($uri)
{
    return (new Modifiers\AddTrailingSlash())->process($uri);
}

/**
 * Append a label or a host to the current URI host
 *
 * @see Modifiers\AppendLabel::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $host
 *
 * @return LeagueUriInterface|UriInterface
 */
function append_host($uri, string $host)
{
    return (new Modifiers\AppendLabel($host))->process($uri);
}

/**
 * Append an new segment or a new path to the URI path
 *
 * @see Modifiers\AppendSegment::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $path
 *
 * @return LeagueUriInterface|UriInterface
 */
function append_path($uri, string $path)
{
    return (new Modifiers\AppendSegment($path))->process($uri);
}

/**
 * Add the new query data to the existing URI query
 *
 * @see Modifiers\AppendQuery::modifyQuery()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $query
 *
 * @return LeagueUriInterface|UriInterface
 */
function append_query($uri, string $query)
{
    return (new Modifiers\AppendQuery($query))->process($uri);
}

/**
 * Convert the URI host part to its ascii value
 *
 * @see Modifiers\HostToAscii::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function host_to_ascii($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\HostToAscii();

    return $modifier->process($uri);
}

/**
 * Convert the URI host part to its unicode value
 *
 * @see Modifiers\HostToUnicode::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function host_to_unicode($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\HostToUnicode();

    return $modifier->process($uri);
}

/**
 * Tell whether the URI represents an absolute URI
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return bool
 */
function is_absolute($uri): bool
{
    return '' !== normalize($uri)->getScheme();
}

/**
 * Tell whether the URI represents an absolute path
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return bool
 */
function is_absolute_path($uri): bool
{
    $uri = normalize($uri);

    return '' === $uri->getScheme()
        && '' === $uri->getAuthority()
        && '/' === substr($uri->getPath(), 0, 1);
}

/**
 * Tell whether the URI represents a network path
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return bool
 */
function is_network_path($uri): bool
{
    $uri = normalize($uri);

    return '' === $uri->getScheme()
        && '' !== $uri->getAuthority();
}

/**
 * Tell whether the URI represents a relative path
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return bool
 */
function is_relative_path($uri): bool
{
    $uri = normalize($uri);

    return '' === $uri->getScheme()
        && '' === $uri->getAuthority()
        && '/' !== substr($uri->getPath(), 0, 1);
}

/**
 * Tell whether both URI refers to the same document
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param LeagueUriInterface|UriInterface $base_uri
 *
 * @return bool
 */
function is_same_document($uri, $base_uri): bool
{
    return normalize($uri)->withFragment('')->__toString()
        === normalize($base_uri)->withFragment('')->__toString();
}

/**
 * Merge a new query with the existing URI query
 *
 * @see Modifiers\MergeQuery::modifyQuery()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $query
 *
 * @return LeagueUriInterface|UriInterface
 */
function merge_query($uri, string $query)
{
    return (new Modifiers\MergeQuery($query))->process($uri);
}

/**
 * Normalize an URI for comparison
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function normalize($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\Normalize();

    return $modifier->process($uri);
}

/**
 * Convert the Data URI path to its ascii form
 *
 * @see Modifiers\DataUriToAscii::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function path_to_ascii($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\DataUriToAscii();

    return $modifier->process($uri);
}

/**
 * Convert the Data URI path to its binary (base64encoded) form
 *
 * @see Modifiers\DataUriToBinary::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function path_to_binary($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\DataUriToBinary();

    return $modifier->process($uri);
}

/**
 * Prepend a label or a host to the current URI host
 *
 * @see Modifiers\PrependLabel::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $host
 *
 * @return LeagueUriInterface|UriInterface
 */
function prepend_host($uri, string $host)
{
    return (new Modifiers\PrependLabel($host))->process($uri);
}

/**
 * Prepend an new segment or a new path to the URI path
 *
 * @see Modifiers\PrependSegment::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $path
 *
 * @return LeagueUriInterface|UriInterface
 */
function prepend_path($uri, string $path)
{
    return (new Modifiers\PrependSegment($path))->process($uri);
}

/**
 * Relativize an URI against a base URI
 *
 * @see Modifiers\Relativize::process()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param LeagueUriInterface|UriInterface $base_uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function relativize($uri, $base_uri)
{
    return (new Modifiers\Relativize($base_uri))->process($uri);
}

/**
 * Remove a basepath from the URI path
 *
 * @see Modifiers\RemoveBasePath::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $path
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_basepath($uri, string $path)
{
    return (new Modifiers\RemoveBasePath($path))->process($uri);
}

/**
 * Remove dot segments from the URI path
 *
 * @see Modifiers\RemoveDotSegments::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_dot_segments($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\RemoveDotSegments();

    return $modifier->process($uri);
}

/**
 * Remove empty segments from the URI path
 *
 * @see Modifiers\RemoveEmptySegments::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_empty_segments($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\RemoveEmptySegments();

    return $modifier->process($uri);
}

/**
 * Remove host labels according to their offset
 *
 * @see Modifiers\RemoveLabels::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param int[]                           $keys
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_labels($uri, array $keys)
{
    return (new Modifiers\RemoveLabels($keys))->process($uri);
}

/**
 * Remove the leading slash from the URI path
 *
 * @see Modifiers\RemoveLeadingSlash::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_leading_slash($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\RemoveLeadingSlash();

    return $modifier->process($uri);
}

/**
 * Remove query data according to their key name
 *
 * @see Modifiers\RemoveQueryParams::modifyQuery()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string[]                        $keys
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_params($uri, array $keys)
{
    return (new Modifiers\RemoveQueryParams($keys))->process($uri);
}

/**
 * Remove query data according to their key name
 *
 * @see Modifiers\RemoveQueryKeys::modifyQuery()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string[]                        $keys
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_pairs($uri, array $keys)
{
    return (new Modifiers\RemoveQueryKeys($keys))->process($uri);
}

/**
 * Remove the root label to the URI
 *
 * @see Modifiers\RemoveRootLabel::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_root_label($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\RemoveRootLabel();

    return $modifier->process($uri);
}

/**
 * Remove the trailing slash from the URI path
 *
 * @see Modifiers\RemoveTrailingSlash::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_trailing_slash($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\RemoveTrailingSlash();

    return $modifier->process($uri);
}

/**
 * Remove path segments from the URI path according to their offsets
 *
 * @see Modifiers\RemoveSegments::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param int[]                           $keys
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_segments($uri, array $keys)
{
    return (new Modifiers\RemoveSegments($keys))->process($uri);
}

/**
 * Remove the host zone identifier
 *
 * @see Modifiers\RemoveZoneIdentifier::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function remove_zone_id($uri)
{
    static $modifier;

    $modifier = $modifier ?? new Modifiers\RemoveZoneIdentifier();

    return $modifier->process($uri);
}

/**
 * Replace the URI path basename
 *
 * @see Modifiers\Basename::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $path
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_basename($uri, string $path)
{
    return (new Modifiers\Basename($path))->process($uri);
}

/**
 * Replace the data URI path parameters
 *
 * @see Modifiers\DataUriParameters::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $parameters
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_data_uri_parameters($uri, string $parameters)
{
    return (new Modifiers\DataUriParameters($parameters))->process($uri);
}

/**
 * Replace the URI path dirname
 *
 * @see Modifiers\Dirname::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $path
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_dirname($uri, string $path)
{
    return (new Modifiers\Dirname($path))->process($uri);
}

/**
 * Replace the URI path basename extension
 *
 * @see Modifiers\Extension::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $extension
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_extension($uri, string $extension)
{
    return (new Modifiers\Extension($extension))->process($uri);
}

/**
 * Replace a label of the current URI host
 *
 * @see Modifiers\ReplaceLabel::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param int                             $offset
 * @param string                          $host
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_label($uri, int $offset, string $host)
{
    return (new Modifiers\ReplaceLabel($offset, $host))->process($uri);
}

/**
 * Replace the host public suffix part
 *
 * @see Modifiers\PublicSuffix::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $host
 * @param null|Rules                      $resolver
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_publicsuffix($uri, string $host, Rules $resolver = null)
{
    return (new Modifiers\PublicSuffix($host, $resolver))->process($uri);
}

/**
 * Replace the host registrabledomain
 *
 * @see Modifiers\RegisterableDomain::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $host
 * @param null|Rules                      $resolver
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_registrabledomain($uri, string $host, Rules $resolver = null)
{
    return (new Modifiers\RegisterableDomain($host, $resolver))->process($uri);
}

/**
 * Replace a segment from the URI path according its offset
 *
 * @see Modifiers\PrependSegment::modifyPath()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param int                             $offset
 * @param string                          $path
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_segment($uri, int $offset, string $path)
{
    return (new Modifiers\ReplaceSegment($offset, $path))->process($uri);
}

/**
 * Replace the host subdomain
 *
 * @see Modifiers\Subdomain::modifyHost()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param string                          $host
 * @param null|Rules                      $resolver
 *
 * @return LeagueUriInterface|UriInterface
 */
function replace_subdomain($uri, string $host, Rules $resolver = null)
{
    return (new Modifiers\Subdomain($host, $resolver))->process($uri);
}

/**
 * Resolve an URI against a base URI
 *
 * @see Modifiers\Resolve::process()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param LeagueUriInterface|UriInterface $base_uri
 *
 * @return LeagueUriInterface|UriInterface
 */
function resolve($uri, $base_uri)
{
    return (new Modifiers\Resolve($base_uri))->process($uri);
}

/**
 * Sort the URI query by keys
 *
 * @see Modifiers\KsortQuery::modifyQuery()
 *
 * @param LeagueUriInterface|UriInterface $uri
 * @param int|callable                    $sort The algorithm used to sort the query keys
 *
 * @return LeagueUriInterface|UriInterface
 */
function sort_query($uri, $sort = SORT_REGULAR)
{
    return (new Modifiers\KsortQuery($sort))->process($uri);
}

/**
 * Returns the RFC3986 string representation of the given URI object
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return string
 */
function uri_to_rfc3986($uri)
{
    list($remaining_uri, $fragment) = explode('#', (string) $uri, 2) + ['', null];
    list(, $query) = explode('?', $remaining_uri, 2) + ['', null];

    $formatter = new Modifiers\Formatter();
    $formatter->setEncoding(Modifiers\Formatter::RFC3986_ENCODING);
    $formatter->preserveFragment(null !== $fragment);
    $formatter->preserveQuery(null !== $query);

    return $formatter($uri);
}

/**
 * Returns the RFC3987 string representation of the given URI object
 *
 * @param LeagueUriInterface|UriInterface $uri
 *
 * @return string
 */
function uri_to_rfc3987($uri): string
{
    list($remaining_uri, $fragment) = explode('#', (string) $uri, 2) + ['', null];
    list(, $query) = explode('?', $remaining_uri, 2) + ['', null];

    $formatter = new Modifiers\Formatter();
    $formatter->setEncoding(Modifiers\Formatter::RFC3987_ENCODING);
    $formatter->preserveFragment(null !== $fragment);
    $formatter->preserveQuery(null !== $query);

    return $formatter($uri);
}
