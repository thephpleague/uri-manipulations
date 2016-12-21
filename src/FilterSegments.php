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

use League\Uri\Components\HierarchicalPath;

/**
 * Filter the path component segments
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class FilterSegments extends ManipulatePath
{
    /**
     * the filter flag
     *
     * @var int
     */
    protected $flag;

    /**
     * a filter callable to filter the
     * data to keep
     *
     * @var callable
     */
    protected $callable;

    /**
     * New instance
     *
     * @param callable $callable
     * @param int      $flag
     */
    public function __construct(callable $callable, int $flag = 0)
    {
        $this->callable = $callable;
        $this->flag = $this->filterFlag($flag);
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyPath(string $str): string
    {
        return (string) (new HierarchicalPath($str))->filter($this->callable, $this->flag);
    }
}
