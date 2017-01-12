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
declare(strict_types=1);

namespace League\Uri\Modifiers;

/**
 * A class to normalize URI objects
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   1.0.0
 */
class Normalize extends AbstractUriMiddleware
{
    /**
     * @inheritdoc
     */
    public function process($uri)
    {
        $this->assertUriObject($uri);
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
        if (null === $defaults) {
            $defaults = new Pipeline([
                new HostToAscii(),
                new KsortQuery(),
                new DecodeUnreservedCharacters(),
            ]);
        }

        return $defaults;
    }
}
