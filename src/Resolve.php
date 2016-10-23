<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @copyright  2016 Ignace Nyamagana Butera
 * @license    https://github.com/thephpleague/uri-components/blob/master/LICENSE (MIT License)
 * @version    1.0.0
 * @link       https://github.com/thephpleague/uri-components
 */
namespace League\Uri\Modifiers;

use League\Uri\Components\Path;
use League\Uri\Schemes\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Resolve an URI according to a base URI using
 * RFC3986 rules
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Resolve extends ManipulateUri
{
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
        $this->assertUriObject($base_uri);
        $this->base_uri = $base_uri;
    }

    /**
     * @inheritdoc
     */
    public function __invoke($target)
    {
        $meta = uri_reference($target);
        $target_path = $target->getPath();
        if ($meta['absolute_uri']) {
            return $target
                ->withPath((new Path($target_path))->withoutDotSegments()->__toString());
        }

        if ($meta['network_path']) {
            return $target
                ->withScheme($this->base_uri->getScheme())
                ->withPath((new Path($target_path))->withoutDotSegments()->__toString());
        }

        $user_info = explode(':', $this->base_uri->getUserInfo(), 2);
        $components = $this->resolvePathAndQuery($target_path, $target->getQuery());

        return $target
            ->withPath($this->formatPath($components['path']))
            ->withQuery($components['query'])
            ->withHost($this->base_uri->getHost())
            ->withPort($this->base_uri->getPort())
            ->withUserInfo((string) array_shift($user_info), array_shift($user_info))
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
    protected function resolvePathAndQuery($path, $query)
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
    protected function mergePath($path)
    {
        $base_path = $this->base_uri->getPath();
        if ('' !== $this->base_uri->getAuthority() && '' === $base_path) {
            return (string) (new Path($path))->withLeadingSlash();
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
    protected function formatPath($path)
    {
        $path = (new Path($path))->withoutDotSegments();
        if ('' !== $this->base_uri->getAuthority() && '' !== $path->__toString()) {
            $path = $path->withLeadingSlash();
        }

        return (string) $path;
    }
}
