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
    /**
     * New instance from invalid URI
     *
     * @param mixed $uri invalid uri
     *
     * @return self
     */
    public static function fromInvalidUri($uri)
    {
        return new self(sprintf('Expected data to be a valid URI object; received "%s"', is_object($uri) ? get_class($uri) : gettype($uri)));
    }

    /**
     * New instance from invalid URI
     *
     * @param string $interface required interface
     * @param mixed  $uri       invalid uri
     *
     * @return self
     */
    public static function fromInvalidInterface(string $interface, $uri)
    {
        return new self(sprintf('The returned URI must be a "%s"; received "%s"', $interface, is_object($uri) ? get_class($uri) : gettype($uri)));
    }
}
