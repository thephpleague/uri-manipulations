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
use League\Uri\Components\Path;
use League\Uri\Components\Query;
use League\Uri\Interfaces\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Abstract Class for all pipeline
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
abstract class ManipulateUri
{
    /**
     * Invalid Characters
     *
     * @see http://tools.ietf.org/html/rfc3986#section-2
     *
     * @var string
     */
    const INVALID_CHARS = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x7F";

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
     * Return a Uri object modified according to the modifier
     *
     * @param Uri|UriInterface $payload
     *
     * @return Uri|UriInterface
     */
    abstract public function __invoke($payload);

    /**
     * validate a string
     *
     * @param mixed $str the value to evaluate as a string
     *
     * @throws InvalidArgumentException if the submitted data can not be converted to string
     *
     * @return string
     */
    protected function validateString(string $str): string
    {
        if (strlen($str) !== strcspn($str, self::INVALID_CHARS)) {
            throw new InvalidArgumentException(sprintf(
                'the submitted string `%s` contains invalid characters',
                $str
            ));
        }

        return $str;
    }

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
               (is_object($uri) ? get_class($uri) : gettype($uri))
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
    protected function filterHost(string $label): Host
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
    protected function filterFlag(int $flag): int
    {
        if (isset(static::$flagList[$flag])) {
            return $flag;
        }

        throw new InvalidArgumentException('Invalid Flag');
    }

    /**
     * Filter and validate the query string
     *
     * @param string $query
     *
     * @return Query
     */
    protected function filterQuery(string $query): Query
    {
        return new Query($this->validateString($query));
    }

    /**
     * Filter and validate a hierarchical path
     *
     * @param string $path
     *
     * @return HierarchicalPath
     */
    protected function filterSegment(string $path): HierarchicalPath
    {
        return new HierarchicalPath($this->validateString($path));
    }

    /**
     * Filter and validate a generic path
     *
     * @param string $path the data to be merged query can be
     *
     * @return Path
     */
    protected function filterPath(string $path): Path
    {
        return new Path($this->validateString($path));
    }

    /**
     * Filter and validate a data URI path
     *
     * @param string $path the data to be merged query can be
     *
     * @return Path
     */
    protected function filterDataPath(string $path): DataPath
    {
        return new DataPath($this->validateString($path));
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

        throw new InvalidArgumentException('The submitted sorting algorithm is invalid');
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
    protected function filterParamaters(string $parameters): string
    {
        return $this->filterDataPath('text/plain;charset=us-ascii,')
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
    protected function filterExtension(string $extension): string
    {
        $extension = $this->validateString($extension);
        if (0 === strpos($extension, '.') || false !== strpos($extension, '/')) {
            throw new InvalidArgumentException(
                'extension must be string sequence without a leading `.` and the path separator `/` characters'
            );
        }

        return trim($extension);
    }
}
