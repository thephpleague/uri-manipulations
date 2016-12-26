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

use League\Uri\Components\Query;

/**
 * Append a quey string to the URI query
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class AppendQuery extends ManipulateQuery
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
    public function __construct(string $query)
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
    protected function modifyQuery(string $str): string
    {
        $new = $this->filterQuery($str);
        foreach ($this->query as $key => $value) {
            $new = $new->append($key, $value);
        }

        return (string) $new;
    }
}
