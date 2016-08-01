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
 * Append a segment to the URI path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class AppendSegment extends PathManipulator
{
    /**
     * A HierarchicalPath object
     *
     * @var HierarchicalPath
     */
    protected $segment;

    /**
     * New instance
     *
     * @param string $segment
     */
    public function __construct($segment)
    {
        $this->segment = $this->filterSegment($segment);
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
        return (string) $this->segment
            ->withContent($str)
            ->append((string) $this->segment);
    }
}
