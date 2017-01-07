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

/**
 * Remove some keys from the Query string
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class RemoveQueryKeys extends ManipulateQuery
{
    /**
     * The list of keys to remove
     *
     * @var array
     */
    protected $keys = [];

    /**
     * New instance
     *
     * @param string[] $keys
     */
    public function __construct(array $keys)
    {
        $this->keys = array_map([$this, 'validateString'], $keys);
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
        return (string) $this->filterQuery($str)->withoutPairs($this->keys);
    }
}
