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

use League\Uri\Components\Path;

/**
 * Remove the trailing slash to the URI path
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class RemoveTrailingSlash extends PathManipulator
{
    /**
     * Modify a URI part
     *
     * @param string $path the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyPath($path)
    {
        return (string) (new Path($path))->withoutTrailingSlash();
    }
}
