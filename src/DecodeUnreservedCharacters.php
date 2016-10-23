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

use League\Uri\Schemes\Uri;
use Psr\Http\Message\UriInterface;

/**
 * A class to Decode URI parts unreserved characters
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.2.0
 */
class DecodeUnreservedCharacters extends ManipulateUri
{
    /**
     * Return a Uri object modified according to the modifier
     *
     * @param Uri|UriInterface $uri
     *
     * @return Uri|UriInterface
     */
    public function __invoke($uri)
    {
        $this->assertUriObject($uri);

        foreach (['Path', 'Query', 'Fragment'] as $part) {
            $uri = $this->decodeUriPart($uri, $part);
        }

        return $uri;
    }

    /**
     * Decode an URI part
     *
     * @param Uri|UriInterface $uri
     * @param string           $property
     *
     * @return Uri|UriInterface
     */
    protected function decodeUriPart($uri, $property)
    {
        $decoder = function (array $matches) {
            return rawurldecode($matches[0]);
        };

        $value = preg_replace_callback(
            ',%('.self::$unreservedCharsEncoded.'),i',
            $decoder,
            call_user_func([$uri, 'get'.$property])
        );

        return call_user_func([$uri, 'with'.$property], $value);
    }
}
