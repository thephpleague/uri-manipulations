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
 * Append a segment to the URI path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class RemoveBasePath extends ManipulatePath
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
     * @param string $segment
     */
    public function __construct($basepath)
    {
        $this->basepath = $this->filterSegment($basepath);
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyPath($str)
    {
        if (in_array((string) $this->basepath, ['', '/'], true)) {
            return $str;
        }

        if ('' != $str && '/' !== $str[0]) {
            $str = '/'.$str;
        }

        if ('' == $str || 0 !== strpos($str, (string) $this->basepath)) {
            return $str;
        }

        return (string) $this->basepath->withContent($str)->without($this->basepath->keys());
    }
}
