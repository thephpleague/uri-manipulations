<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package   League.uri
 * @author    Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @copyright 2013-2015 Ignace Nyamagana Butera
 * @license   https://github.com/thephpleague/uri/blob/master/LICENSE (MIT License)
 * @version   4.2.0
 * @link      https://github.com/thephpleague/uri/
 */
namespace League\Uri\Manipulations;

/**
 * A class to normalize URI objects
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Normalize extends UriManipulator
{
    /**
     * @inheritdoc
     */
    public function __invoke($uri)
    {
        $this->assertUriObject($uri);
        $modifiers = $this->getDefaultManipulators();
        $path = $uri->getPath();
        if ('' !== $uri->getScheme().$uri->getAuthority()
            || (isset($path[0]) && '/' === $path[0])) {
            return $modifiers->pipe(new RemoveDotSegments())->__invoke($uri);
        }

        return $modifiers->__invoke($uri);
    }

    /**
     * Return the default modifier to apply on any URI object
     *
     * @return array
     */
    protected function getDefaultManipulators()
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
