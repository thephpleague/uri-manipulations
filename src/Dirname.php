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
 * Path component extension modifier
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Dirname extends ManipulatePath
{
    /**
     * The dirname to use for URI modification
     *
     * @var string
     */
    protected $dirname;

    /**
     * New instance
     *
     * @param string $dirname
     */
    public function __construct(string $dirname)
    {
        $this->dirname = (string) $this->filterSegment($dirname);
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
        return (string) (new HierarchicalPath($str))->withDirname($this->dirname);
    }
}
