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
namespace League\Uri\Modifiers;

use League\Uri\Interfaces\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Resolve an URI according to a base URI using
 * RFC3986 rules
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.2.0
 */
class Relativize extends ManipulateUri
{
    /**
     * Base URI
     *
     * @var Uri|UriInterface
     */
    protected $base_uri;

    /**
     * New instance
     *
     * @param Uri|UriInterface $base_uri
     */
    public function __construct($base_uri)
    {
        $this->assertUriObject($base_uri);
        $this->base_uri = $this->hostToAscii($base_uri);
    }

    /**
     * Convert the Uri host component to its ascii version
     *
     * @param Uri|UriInterface $uri
     *
     * @return Uri|UriInterface
     */
    protected function hostToAscii($uri)
    {
        static $modifier;
        if (null === $modifier) {
            $modifier = new HostToAscii();
        }

        return $modifier($uri);
    }

    /**
     * Return a Uri object modified according to the modifier
     *
     * @param Uri|UriInterface $payload
     *
     * @return Uri|UriInterface
     */
    public function __invoke($payload)
    {
        $this->assertUriObject($payload);
        if (!$this->isRelativizable($payload)) {
            return $payload;
        }

        $payload = $payload->withScheme('')->withPort(null)->withUserInfo('')->withHost('');

        $target_path = $payload->getPath();
        if ($target_path !== $this->base_uri->getPath()) {
            return $payload->withPath($this->relativizePath($target_path));
        }

        if ($payload->getQuery() === $this->base_uri->getQuery()) {
            return $payload->withPath('')->withQuery('');
        }

        if ('' === $payload->getQuery()) {
            return $payload->withPath($this->formatPathWithEmptyBaseQuery($target_path));
        }

        return $payload->withPath('');
    }

    /**
     * Tell whether the submitted URI object can be relativize
     *
     * @param Uri|UriInterface $payload
     *
     * @return bool
     */
    protected function isRelativizable($payload)
    {
        $payload = $this->hostToAscii($payload);

        return $this->base_uri->getScheme() === $payload->getScheme()
            && $this->base_uri->getAuthority() === $payload->getAuthority()
            && !uri_reference($payload)['relative_path'];
    }

    /**
     * Relative the URI for a authority-less target URI
     *
     * @param string $path
     *
     * @return string
     */
    protected function relativizePath($path)
    {
        $base_segments = $this->getSegments($this->base_uri->getPath());
        $target_segments = $this->getSegments($path);
        $target_basename = array_pop($target_segments);
        array_pop($base_segments);
        foreach ($base_segments as $offset => $segment) {
            if (!isset($target_segments[$offset]) || $segment !== $target_segments[$offset]) {
                break;
            }
            unset($base_segments[$offset], $target_segments[$offset]);
        }
        $target_segments[] = $target_basename;

        return $this->formatPath(
            str_repeat('../', count($base_segments)).implode('/', $target_segments)
        );
    }

    /**
     * returns the path segments
     *
     * @param string $path
     *
     * @return array
     */
    protected function getSegments($path)
    {
        if ('' !== $path && '/' === $path[0]) {
            $path = substr($path, 1);
        }

        return explode('/', $path);
    }

    /**
     * Formatting the path to keep a valid URI
     *
     * @param string $path
     *
     * @return string
     */
    protected function formatPath($path)
    {
        if ('' === $path) {
            $base_path = $this->base_uri->getPath();
            return in_array($base_path, ['', '/']) ? $base_path : './';
        }

        if (false === ($colon_pos = strpos($path, ':'))) {
            return $path;
        }

        $slash_pos = strpos($path, '/');
        if (false === $slash_pos || $colon_pos < $slash_pos) {
            return "./$path";
        }

        return $path;
    }

    /**
     * Formatting the path to keep a resolvable URI
     *
     * @param string $path
     *
     * @return string
     */
    protected function formatPathWithEmptyBaseQuery($path)
    {
        $target_segments = $this->getSegments($path);
        $basename = end($target_segments);

        return '' === $basename ? './' : $basename;
    }
}
