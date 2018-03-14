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

namespace League\Uri\Modifiers;

/**
 * Abstract Class to modify the Path component
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
trait PathMiddlewareTrait
{
    /**
     * @inheritdoc
     */
    protected function execute($uri)
    {
        $path = $this->modifyPath($uri->getPath());
        if ('' != $uri->getAuthority() && '' != $path && '/' != $path[0]) {
            $path = '/'.$path;
        }

        return $uri->withPath($path);
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    abstract protected function modifyPath(string $str): string;
}
