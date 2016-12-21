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

use League\Uri\Components\DataPath;

/**
 * Data Uri Paramaters Manipulator
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class DataUriParameters extends ManipulatePath
{
    /**
     * A DataPath object
     *
     * @var string
     */
    protected $parameters;

    /**
     * New instance
     *
     * @param string $parameters the data to be used
     *
     */
    public function __construct(string $parameters)
    {
        $this->parameters = $this->filterParamaters($parameters);
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
        return (new DataPath($str))->withParameters($this->parameters)->__toString();
    }
}
