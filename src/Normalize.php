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

/**
 * A class to normalize URI objects
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Normalize extends ManipulateUri
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
