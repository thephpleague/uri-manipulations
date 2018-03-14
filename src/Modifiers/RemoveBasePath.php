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

use League\Uri\Components\HierarchicalPath;

/**
 * Removes the basepath from the URI path
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class RemoveBasePath implements UriMiddlewareInterface
{
    use PathMiddlewareTrait;
    use UriMiddlewareTrait;

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
        $this->basepath = $this->filterSegment($basepath);
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
        if (in_array((string) $this->basepath, ['', '/'], true)) {
            return $str;
        }

        if ('' != $str && '/' !== $str[0]) {
            $str = '/'.$str;
        }

        if ('' == $str || 0 !== strpos($str, (string) $this->basepath)) {
            return $str;
        }

        return (string) $this->filterSegment($str)->withoutSegments($this->basepath->keys());
    }
}
