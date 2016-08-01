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

/**
 * Add or Update the Query string from the URI object
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class MergeQuery extends QueryManipulator
{
    /**
     * A Query object
     *
     * @var Query
     */
    protected $query;

    /**
     * New Instance
     *
     * @param string $query
     */
    public function __construct($query)
    {
        $this->query = $this->filterQuery($query);
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyQuery($str)
    {
        return (string) $this->query
            ->withContent($str)
            ->merge((string) $this->query);
    }
}
