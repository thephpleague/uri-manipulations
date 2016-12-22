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
 * Remove the trailing slash to the URI path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class RemoveTrailingSlash extends ManipulatePath
{
    /**
     * Modify a URI part
     *
     * @param string $path the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyPath(string $path): string
    {
        return (string) $this->filterPath($path)->withoutTrailingSlash();
    }
}
