<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package   League.uri
 * @author    Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @copyright 2016 Ignace Nyamagana Butera
 * @license   https://github.com/thephpleague/uri/blob/master/LICENSE (MIT License)
 * @version   4.2.0
 * @link      https://github.com/thephpleague/uri/
 */
declare(strict_types=1);

namespace League\Uri\Modifiers;

use InvalidArgumentException;
use League\Uri;
use League\Uri\Interfaces\Uri as LeagueUri;
use Psr\Http\Message\UriInterface;

/**
 *
 * DEPRECATION WARNING! This method will be removed in the next major point release
 *
 * @deprecated deprecated since version 1.1.0
 *
 * @see \League\Uri\is_absolute
 * @see \League\Uri\is_network_path
 * @see \League\Uri\is_absolute_path
 * @see \League\Uri\is_relative_path
 * @see \League\Uri\is_same_document
 *
 * A function to give information about URI Reference
 *
 * This function returns an associative array representing the URI Reference information:
 * each key represents a given state and each value is a boolean to indicate the current URI
 * status against the declared state.
 *
 * <ul>
 * <li>absolute_uri: Tell whether the URI is absolute
 * <li>network_path: Tell whether the URI is a network-path relative reference
 * <li>absolute_path: Tell whether the URI is a absolute-path relative reference
 * <li>relative_path: Tell whether the URI is a relative-path relative reference
 * <li>same_document: Tell whether the URI is a same-document relative reference
 * </ul>
 *
 * @link https://tools.ietf.org/html/rfc3986#section-4.2
 * @link https://tools.ietf.org/html/rfc3986#section-4.3
 * @link https://tools.ietf.org/html/rfc3986#section-4.4
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 *
 * @param LeagueUri|UriInterface      $uri      The uri to get reference info from
 * @param LeagueUri|UriInterface|null $base_uri The base uri to use to get same document reference info
 *
 * @throws InvalidArgumentException if the submitted Uri is invalid
 *
 * @return array
 */
function uri_reference($uri, $base_uri = null): array
{
    $infos = [
        'absolute_uri' => false,
        'network_path' => false,
        'absolute_path' => false,
        'relative_path' => false,
        'same_document' => false,
    ];

    $uri = Uri\normalize($uri)->withFragment('');

    if (null !== $base_uri) {
        $infos['same_document'] = (string) $uri === (string) Uri\normalize($base_uri)->withFragment('');
    }

    if ('' !== $uri->getScheme()) {
        $infos['absolute_uri'] = true;

        return $infos;
    }

    if ('' !== $uri->getAuthority()) {
        $infos['network_path'] = true;

        return $infos;
    }

    $path = $uri->getPath();
    if ('/' === substr($path, 0, 1)) {
        $infos['absolute_path'] = true;

        return $infos;
    }

    $infos['relative_path'] = true;

    return $infos;
}
