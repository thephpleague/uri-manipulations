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

use League\Uri\Schemes\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Abstract Class for all pipeline
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
abstract class UriManipulator
{
    use Validator;

    /**
     * Return a Uri object modified according to the modifier
     *
     * @param Uri|UriInterface $payload
     *
     * @return Uri|UriInterface
     */
    abstract public function __invoke($payload);
}
