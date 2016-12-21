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
use League\Uri\Components\HierarchicalPath;

/**
 * Path component extension modifier
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Basename extends ManipulatePath
{
    /**
     * The basename to use for URI modification
     *
     * @var string
     */
    protected $basename;

    /**
     * New instance
     *
     * @param string $basename
     */
    public function __construct(string $basename)
    {
        $basename = (string) $this->filterSegment($basename);
        if (false !== strpos($basename, '/')) {
            throw new InvalidArgumentException('The basename can not contain the separator');
        }

        $this->basename = $basename;
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
        return (string) (new HierarchicalPath($str))->withBasename($this->basename);
    }
}
