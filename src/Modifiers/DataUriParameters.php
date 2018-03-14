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

/**
 * Data Uri Paramaters Manipulator
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class DataUriParameters implements UriMiddlewareInterface
{
    use PathMiddlewareTrait;
    use UriMiddlewareTrait;

    /**
     * The parameters to add
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
        return (string) $this->filterDataPath($str)->withParameters($this->parameters);
    }
}
