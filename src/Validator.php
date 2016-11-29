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

use InvalidArgumentException;
use League\Uri\Components\DataPath;
use League\Uri\Components\HierarchicalPath;
use League\Uri\Components\Host;
use League\Uri\Components\Query;
use League\Uri\Interfaces\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Flag trait to Filter League\Uri Collections
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 * @internal
 */
trait Validator
{
    /**
     * Invalid Characters
     *
     * @see http://tools.ietf.org/html/rfc3986#section-2
     *
     * @var string
     */
    protected static $invalidUriChars = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x7F";

    /**
     * RFC3986 unreserved characters encoded regular expression pattern
     *
     * @see http://tools.ietf.org/html/rfc3986#section-2.3
     *
     * @var string
     */
    protected static $unreservedCharsEncoded = '2[D|E]|3[0-9]|4[1-9|A-F]|5[0-9|A|F]|6[1-9|A-F]|7[0-9|E]';

    /**
     * validate a string
     *
     * @param mixed $str the value to evaluate as a string
     *
     * @throws InvalidArgumentException if the submitted data can not be converted to string
     *
     * @return string
     */
    protected static function validateString($str)
    {
        if (!is_string($str)) {
            throw new InvalidArgumentException(sprintf(
                'Expected data to be a string; received "%s"',
                (is_object($str) ? get_class($str) : gettype($str))
            ));
        }

        if (strlen($str) !== strcspn($str, self::$invalidUriChars)) {
            throw new InvalidArgumentException(sprintf(
                'the submitted string `%s` contains invalid characters',
                $str
            ));
        }

        return $str;
    }

    /**
     * Available flags
     *
     * @var array
     */
    protected static $flagList = [
        0 => 1,
        ARRAY_FILTER_USE_BOTH => 1,
        ARRAY_FILTER_USE_KEY => 1,
    ];

    /**
     * Assert the submitted object is a UriInterface object
     *
     * @param Uri|UriInterface $uri
     *
     * @throws InvalidArgumentException if the object does not implemet PSR-7 UriInterface
     */
    protected function assertUriObject($uri)
    {
        if (!$uri instanceof Uri && !$uri instanceof UriInterface) {
            throw new InvalidArgumentException(sprintf(
                'URI passed must implement PSR-7 or League\Uri Uri interface; received "%s"',
                gettype($uri)
            ));
        }
    }

    /**
     * Filter and validate the host string
     *
     * @param string $label the data to validate
     *
     * @return Host
     */
    protected function filterLabel($label)
    {
        return new Host($this->validateString($label));
    }

    /**
     * Filter and validate the host string
     *
     * @param int $flag the data to validate
     *
     * @throws InvalidArgumentException for invalid flag
     *
     * @return int
     */
    protected function filterFlag($flag)
    {
        if (isset(static::$flagList[$flag])) {
            return $flag;
        }

        throw new InvalidArgumentException('Invalid Flag');
    }

    /**
     * Filter and validate the query data
     *
     * @param string $query the data to be merged
     *
     * @return Query
     */
    protected function filterQuery($query)
    {
        return new Query($this->validateString($query));
    }

    /**
     * Filter and validate the offset key
     *
     * @param int $offset
     *
     * @throws InvalidArgumentException if the Offset key is invalid
     *
     * @return int
     */
    protected function filterOffset($offset)
    {
        $offset = filter_var($offset, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if (false !== $offset) {
            return $offset;
        }

        throw new InvalidArgumentException('The submitted index is invalid');
    }

    /**
     * Filter and validate the path data
     *
     * @param string $path the data to be merged query can be
     *
     * @return HierarchicalPath
     */
    protected function filterSegment($path)
    {
        return new HierarchicalPath($this->validateString($path));
    }

    /**
     * Validate the sorting Parameter
     *
     * @param callable|int $sort a PHP sort flag constant or a comparison function
     *                           which must return an integer less than, equal to,
     *                           or greater than zero if the first argument is
     *                           considered to be respectively less than, equal to,
     *                           or greater than the second.
     *
     * @throws InvalidArgumentException if the sort argument is invalid
     *
     * @return callable|int
     */
    protected function filterAlgorithm($sort)
    {
        if (is_callable($sort) || is_int($sort)) {
            return $sort;
        }

        throw new InvalidArgumentException('The submitted keys are invalid');
    }

    /**
     * Filter and validate the parameters
     *
     * @param string $parameters the data to be used
     *
     * @throws InvalidArgumentException if the value is invalid
     *
     * @return string
     */
    protected function filterParamaters($parameters)
    {
        return (new DataPath('text/plain;charset=us-ascii,'))
            ->withParameters($parameters)
            ->getParameters();
    }

    /**
     * filter and validate the extension to use
     *
     * @param string $extension
     *
     * @throws InvalidArgumentException if the extension is not valid
     *
     * @return string
     */
    protected function filterExtension($extension)
    {
        if (0 === strpos($extension, '.') || false !== strpos($extension, '/')) {
            throw new InvalidArgumentException(
                'extension must be string sequence without a leading `.` and the path separator `/` characters'
            );
        }

        $extension = filter_var($extension, FILTER_SANITIZE_STRING, ['options' => FILTER_FLAG_STRIP_LOW]);

        return trim($extension);
    }
}
