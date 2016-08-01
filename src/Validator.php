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

use InvalidArgumentException;
use League\Uri\Components\DataPath;
use League\Uri\Components\HierarchicalPath;
use League\Uri\Components\Host;
use League\Uri\Components\Query;
use League\Uri\Components\Traits\ImmutableComponent;
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
    use ImmutableComponent;

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
