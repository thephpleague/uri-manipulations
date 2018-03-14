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

use League\Uri\PublicSuffix\Rules;

/**
 * Modify the subdomains of the URI host
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class Subdomain implements UriMiddlewareInterface
{
    use HostMiddlewareTrait;
    use UriMiddlewareTrait;

    /**
     * the new subdomain
     *
     * @var string
     */
    protected $label;

    /**
     * @var Rules|null
     */
    protected $resolver;

    /**
     * New instance
     *
     * @param string     $label    the data to be used
     * @param null|Rules $resolver
     *
     */
    public function __construct(string $label, Rules $resolver = null)
    {
        $this->resolver = $resolver;
        $label = $this->filterHost($label, $this->resolver);
        if ($label->isAbsolute()) {
            throw new Exception('The submitted subdomain can not be a fully qualified domaine name');
        }

        $this->label = (string) $label;
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
        return (string) $this->filterHost($str, $this->resolver)->withSubDomain($this->label);
    }
}
