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
 * Replace a Segment from a Path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class ReplaceSegment extends ManipulatePath
{
    /**
     * A HierarchicalPath object
     *
     * @var HierarchicalPath
     */
    protected $segment;

    /**
     * Offset key to replace the content
     *
     * @var int
     */
    protected $offset;

    /**
     * New instance
     *
     * @param int    $offset
     * @param string $segment
     */
    public function __construct(int $offset, string $segment)
    {
        $this->offset = $offset;
        $this->segment = $this->filterSegment($segment);
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
        return (string) $this->segment
            ->withContent($str)
            ->replace($this->offset, (string) $this->segment);
    }
}
