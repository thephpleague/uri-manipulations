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

use League\Uri\Interfaces\Uri;
use Psr\Http\Message\UriInterface;
use function League\Uri\is_relative_path;

/**
 * Resolve an URI according to a base URI using
 * RFC3986 rules
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class Relativize implements UriMiddlewareInterface
{
    use UriMiddlewareTrait;

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

        return $modifier->process($uri);
    }

    /**
     * @inheritdoc
     */
    protected function execute($uri)
    {
        if (!$this->isRelativizable($uri)) {
            return $uri;
        }

        $uri = $uri->withScheme('')->withPort(null)->withUserInfo('')->withHost('');

        $target_path = $uri->getPath();
        if ($target_path !== $this->base_uri->getPath()) {
            return $uri->withPath($this->relativizePath($target_path));
        }

        if ($uri->getQuery() === $this->base_uri->getQuery()) {
            return $uri->withPath('')->withQuery('');
        }

        if ('' === $uri->getQuery()) {
            return $uri->withPath($this->formatPathWithEmptyBaseQuery($target_path));
        }

        return $uri->withPath('');
    }

    /**
     * Tell whether the submitted URI object can be relativize
     *
     * @param Uri|UriInterface $payload
     *
     * @return bool
     */
    protected function isRelativizable($payload): bool
    {
        $payload = $this->hostToAscii($payload);

        return $this->base_uri->getScheme() === $payload->getScheme()
            && $this->base_uri->getAuthority() === $payload->getAuthority()
            && !is_relative_path($payload);
    }

    /**
     * Relative the URI for a authority-less target URI
     *
     * @param string $path
     *
     * @return string
     */
    protected function relativizePath(string $path): string
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
    protected function getSegments(string $path): array
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
    protected function formatPath(string $path): string
    {
        if ('' === $path) {
            $base_path = $this->base_uri->getPath();
            return in_array($base_path, ['', '/'], true) ? $base_path : './';
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
    protected function formatPathWithEmptyBaseQuery(string $path): string
    {
        $target_segments = $this->getSegments($path);
        $basename = end($target_segments);

        return '' === $basename ? './' : $basename;
    }
}
