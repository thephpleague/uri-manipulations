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

use InvalidArgumentException;

/**
 * Base Exception class for League Uri Schemes
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class Exception extends InvalidArgumentException
{
    public static function fromInvalidUri($uri)
    {
        return new self(sprintf(
            'Expected data to be a valid URI object; received "%s"',
            is_object($uri) ? get_class($uri) : gettype($uri)
        ));
    }

    public static function fromInvalidClass($uri, $uri_class)
    {
        return new self(sprintf(
            'The returned URI passed to the next middleware must be a "%s"; received "%s"',
            $uri_class,
            is_object($uri) ? get_class($uri) : gettype($uri)
        ));
    }
}
