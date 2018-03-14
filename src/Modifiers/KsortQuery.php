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
 * Sort the URI object Query
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class KsortQuery implements UriMiddlewareInterface
{
    use QueryMiddlewareTrait;
    use UriMiddlewareTrait;

    /**
     * Sort algorithm use to sort the query string keys
     *
     * @var callable|int
     */
    protected $sort;

    /**
     * New instance
     *
     * @param callable|int $sort a PHP sort flag constant or a comparison function
     *                           which must return an integer less than, equal to,
     *                           or greater than zero if the first argument is
     *                           considered to be respectively less than, equal to,
     *                           or greater than the second.
     */
    public function __construct($sort = SORT_REGULAR)
    {
        $this->sort = $this->filterAlgorithm($sort);
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyQuery(string $str): string
    {
        return (string) $this->filterQuery($str)->ksort($this->sort);
    }
}
