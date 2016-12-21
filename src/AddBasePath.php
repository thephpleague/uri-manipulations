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
 * Add a base path the URI path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class AddBasePath extends ManipulatePath
{
    /**
     * A HierarchicalPath object
     *
     * @var HierarchicalPath
     */
    protected $basepath;

    /**
     * New instance
     *
     * @param string $basepath
     */
    public function __construct(string $basepath)
    {
        $this->basepath = $this->filterSegment($basepath)->withLeadingSlash();
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
        if ('' != $str && '/' !== $str[0]) {
            $str = '/'.$str;
        }

        if (0 === strpos($str, (string) $this->basepath)) {
            return $str;
        }

        return (string) $this->basepath->append($str);
    }
}
