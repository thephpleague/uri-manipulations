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
 * Append a label to the URI host
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class AppendLabel extends HostManipulator
{
    /**
     * A Host object
     *
     * @var Host
     */
    protected $label;

    /**
     * New instance
     *
     * @param string $label the data to be used
     *
     */
    public function __construct($label)
    {
        $this->label = $this->filterLabel($label);
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyHost($str)
    {
        return (string) $this->label
            ->withContent($str)
            ->append($this->label->getContent());
    }
}
