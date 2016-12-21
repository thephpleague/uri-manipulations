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
use League\Uri\Components\Host;

/**
 * Modify the registerable domain part of the URI host
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class RegisterableDomain extends ManipulateHost
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
    public function __construct(string $label)
    {
        $this->label = $this->filterLabel($label);
        if ($this->label->isAbsolute()) {
            throw new InvalidArgumentException('The submitted registerable domain can not be a fully qualified domaine name');
        }
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
        return (string) $this->label
            ->withContent($str)
            ->withRegisterableDomain($this->label->getContent());
    }
}
