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
     * RFC3986 unreserved characters encoded regular expression pattern
     *
     * @see http://tools.ietf.org/html/rfc3986#section-2.3
     *
     * @var string
     */
    const UNRESERVED_CHARS_ENCODED = ',%(2[D|E]|3[0-9]|4[1-9|A-F]|5[0-9|A|F]|6[1-9|A-F]|7[0-9|E]),i';

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

        $decoded = preg_replace_callback(
            self::UNRESERVED_CHARS_ENCODED,
            function (array $matches) {
                return rawurldecode($matches[0]);
            },
            [$uri->getPath(), $uri->getQuery(), $uri->getFragment()]
        );

        return $uri
            ->withPath($decoded[0])
            ->withQuery($decoded[1])
            ->withFragment($decoded[2]);
    }
}
