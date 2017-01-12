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
 * Replace a Segment from a Path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   1.0.0
 */
class ReplaceSegment extends AbstractPathMiddleware
{
    /**
     * The path/segment to inject
     *
     * @var string
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
        $this->segment = (string) $this->filterSegment($segment);
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
        return (string) $this->filterSegment($str)->replaceSegment($this->offset, $this->segment);
    }
}
