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

namespace League\Uri\Modifiers;

use League\Uri\Components\DataPath;
use League\Uri\Components\HierarchicalPath;
use League\Uri\Components\Host;
use League\Uri\Components\Path;
use League\Uri\Components\Query;
use League\Uri\Interfaces\Uri;
use League\Uri\PublicSuffix\Rules;
use Psr\Http\Message\UriInterface;

/**
 * Common methods shared between URI middlewares
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
trait UriMiddlewareTrait
{
    /**
     * Process and return an Uri
     *
     * DEPRECATION WARNING! This method will be removed in the next major point release
     *
     * @deprecated deprecated since version 1.0.1
     *
     * @param Uri|UriInterface $uri
     *
     * @throws Exception If the submitted URI is invalid
     *
     * @return Uri|UriInterface
     *
     * @see UriMiddlewareTrait::process
     */
    public function __invoke($uri)
    {
        return $this->process($uri);
    }

    /**
     * Process and return an Uri
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * an URI instance of the same type that contains the applied modifications.
     *
     * This method MUST be transparent when dealing with error and exceptions.
     * It MUST not alter of silence them apart from validating its own parameters.
     *
     * @param Uri|UriInterface $uri
     *
     * @throws Exception If the submitted or the URI object is invalid
     *
     * @return Uri|UriInterface
     */
    public function process($uri)
    {
        $interface = $this->getUriInterface($uri);
        $new_uri = $this->execute($uri);
        if ($new_uri instanceof $interface) {
            return $new_uri;
        }

        throw Exception::fromInvalidInterface($interface, $new_uri);
    }

    /**
     * Returns the URI object interface
     *
     * @param Uri|UriInterface $uri
     *
     * @throws Exception if the submitted URI object is invalid
     *
     * @return string
     */
    protected function getUriInterface($uri)
    {
        if ($uri instanceof UriInterface) {
            return UriInterface::class;
        }

        if ($uri instanceof Uri) {
            return Uri::class;
        }

        throw Exception::fromInvalidUri($uri);
    }

    /**
     * Process and return an Uri
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * an URI instance that contains the applied modifications.
     *
     * @param Uri|UriInterface $uri
     *
     * @return Uri|UriInterface
     */
    abstract protected function execute($uri);

    /**
     * validate a string
     *
     * @param mixed $str the value to evaluate as a string
     *
     * @throws Exception if the submitted data can not be converted to string
     *
     * @return string
     */
    protected function filterString(string $str): string
    {
        $invalid_chars = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x7F";
        if (strlen($str) !== strcspn($str, $invalid_chars)) {
            throw new Exception(sprintf('the submitted string `%s` contains invalid characters', $str));
        }

        return $str;
    }

    /**
     * Filter and validate the host string
     *
     * @param string     $label    the data to validate
     * @param null|Rules $resolver
     *
     * @return Host
     */
    protected function filterHost(string $label, Rules $resolver = null): Host
    {
        return new Host($this->filterString($label), $resolver);
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
        return new Query($this->filterString($query));
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
        return new HierarchicalPath($this->filterString($path));
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
        return new Path($this->filterString($path));
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
        return new DataPath($this->filterString($path));
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
     * @throws Exception if the sort argument is invalid
     *
     * @return callable|int
     */
    protected function filterAlgorithm($sort)
    {
        if (is_callable($sort) || is_int($sort)) {
            return $sort;
        }

        throw new Exception('The submitted sorting algorithm is invalid');
    }

    /**
     * Filter and validate the parameters
     *
     * @param string $parameters the data to be used
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
     * @throws Exception if the extension is not valid
     *
     * @return string
     */
    protected function filterExtension(string $extension): string
    {
        $extension = $this->filterString($extension);
        if (0 === strpos($extension, '.') || false !== strpos($extension, '/')) {
            throw new Exception(
                'extension must be string sequence without a leading `.` and the path separator `/` characters'
            );
        }

        return trim($extension);
    }

    /**
     * filter and validate the offset list
     *
     * @param array $offsets
     *
     * @throws Exception if the offsets are invalid
     *
     * @return int[]
     */
    protected function filterInt(array $offsets)
    {
        if (array_filter($offsets, 'is_int') === $offsets) {
            return $offsets;
        }

        throw new Exception('The offset list must contain integers only');
    }
}
