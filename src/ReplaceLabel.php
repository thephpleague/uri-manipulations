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

/**
 * Replace a label from a Host
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class ReplaceLabel extends ManipulateHost
{
    /**
     * A Host object
     *
     * @var string
     */
    protected $label;

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
     * @param string $label
     */
    public function __construct(int $offset, string $label)
    {
        $this->offset = $offset;
        $this->label = (string) $this->filterHost($label);
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyHost(string $str): string
    {
        return (string) $this->filterHost($str)->replace($this->offset, $this->label);
    }
}
