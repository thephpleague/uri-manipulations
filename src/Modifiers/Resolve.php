<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @copyright  2016 Ignace Nyamagana Butera
 * @license    https://github.com/thephpleague/uri-manipulations/blob/master/LICENSE (MIT License)
 * @version    1.5.0
 * @link       https://github.com/thephpleague/uri-manipulations
 */
declare(strict_types=1);

namespace League\Uri\Modifiers;

use League\Uri\Interfaces\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Resolve an URI according to a base URI using
 * RFC3986 rules
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class Resolve implements UriMiddlewareInterface
{
    use UriMiddlewareTrait;

    /**
     * The list of keys to remove
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
        if (!$base_uri instanceof UriInterface && !$base_uri instanceof Uri) {
            throw Exception::fromInvalidUri($base_uri);
        }

        $this->base_uri = $base_uri;
    }

    /**
     * @inheritdoc
     */
    protected function execute($uri)
    {
        $target_path = $uri->getPath();
        if ('' !== $uri->getScheme()) {
            return $uri
                ->withPath((string) $this->filterPath($target_path)->withoutDotSegments());
        }

        if ('' !== $uri->getAuthority()) {
            return $uri
                ->withScheme($this->base_uri->getScheme())
                ->withPath((string) $this->filterPath($target_path)->withoutDotSegments());
        }

        list($user, $pass) = explode(':', $this->base_uri->getUserInfo(), 2) + ['', null];
        $components = $this->resolvePathAndQuery($target_path, $uri->getQuery());

        return $uri
            ->withPath($this->formatPath($components['path']))
            ->withQuery($components['query'])
            ->withHost($this->base_uri->getHost())
            ->withPort($this->base_uri->getPort())
            ->withUserInfo($user, $pass)
            ->withScheme($this->base_uri->getScheme());
    }

    /**
     * Resolve the URI for a Authority-less target URI
     *
     * @param string $path  the target path component
     * @param string $query the target query component
     *
     * @return string[]
     */
    protected function resolvePathAndQuery(string $path, string $query): array
    {
        $components = ['path' => $path, 'query' => $query];

        if ('' === $components['path']) {
            $components['path'] = $this->base_uri->getPath();
            if ('' === $components['query']) {
                $components['query'] = $this->base_uri->getQuery();
            }

            return $components;
        }

        if (0 !== strpos($components['path'], '/')) {
            $components['path'] = $this->mergePath($components['path']);
        }

        return $components;
    }

    /**
     * Merging Relative URI path with Base URI path
     *
     * @param string $path
     *
     * @return string
     */
    protected function mergePath(string $path): string
    {
        $base_path = $this->base_uri->getPath();
        if ('' !== $this->base_uri->getAuthority() && '' === $base_path) {
            return (string) $this->filterPath($path)->withLeadingSlash();
        }

        if ('' !== $base_path) {
            $segments = explode('/', $base_path);
            array_pop($segments);
            $path = implode('/', $segments).'/'.$path;
        }

        return $path;
    }

    /**
     * Format the resolved path
     *
     * @param string $path
     *
     * @return string
     */
    protected function formatPath(string $path): string
    {
        $path = $this->filterPath($path)->withoutDotSegments();
        if ('' !== $this->base_uri->getAuthority() && '' !== $path->__toString()) {
            $path = $path->withLeadingSlash();
        }

        return (string) $path;
    }
}
