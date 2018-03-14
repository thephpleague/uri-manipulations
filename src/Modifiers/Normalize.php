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

/**
 * A class to normalize URI objects
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class Normalize implements UriMiddlewareInterface
{
    use UriMiddlewareTrait;

    /**
     * @inheritdoc
     */
    protected function execute($uri)
    {
        $modifiers = $this->getDefaultManipulators();
        $path = $uri->getPath();
        if ('' !== $uri->getScheme().$uri->getAuthority()
            || (isset($path[0]) && '/' === $path[0])) {
            return $modifiers->pipe(new RemoveDotSegments())->process($uri);
        }

        return $modifiers->process($uri);
    }

    /**
     * Return the default modifier to apply on any URI object
     *
     * @return Pipeline
     */
    protected function getDefaultManipulators(): Pipeline
    {
        static $defaults;
        $defaults = $default ?? new Pipeline([
            new HostToAscii(),
            new KsortQuery(),
            new DecodeUnreservedCharacters(),
        ]);

        return $defaults;
    }
}
