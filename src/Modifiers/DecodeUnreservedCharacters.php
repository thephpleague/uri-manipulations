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
 * A class to Decode URI parts unreserved characters
 *
 * @package League\Uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.2.0
 */
class DecodeUnreservedCharacters implements UriMiddlewareInterface
{
    use UriMiddlewareTrait;

    /**
     * RFC3986 unreserved characters encoded regular expression pattern
     *
     * @see http://tools.ietf.org/html/rfc3986#section-2.3
     *
     * @var string
     */
    const UNRESERVED_CHARS_ENCODED = ',%(2[D|E]|3[0-9]|4[1-9|A-F]|5[0-9|A|F]|6[1-9|A-F]|7[0-9|E]),i';

    /**
     * @inheritdoc
     */
    protected function execute($uri)
    {
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
