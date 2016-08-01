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

use InvalidArgumentException;
use League\Uri\Components\Path;

/**
 * Path component typecode modifier
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Typecode extends PathManipulator
{
    const FTP_TYPE_ASCII = 1;

    const FTP_TYPE_BINARY = 2;

    const FTP_TYPE_DIRECTORY = 3;

    const FTP_TYPE_EMPTY = 4;

    /**
     * The typecode selected
     *
     * @var int
     */
    protected $type;

    /**
     * Typecode list
     *
     * @var array
     */
    protected static $typecodeList = [
        self::FTP_TYPE_ASCII => 'a',
        self::FTP_TYPE_BINARY => 'i',
        self::FTP_TYPE_DIRECTORY => 'd',
        self::FTP_TYPE_EMPTY => '',
    ];

    /**
     * Typecode Regular expression
     */
    protected static $typeRegex = ',^(?P<basename>.*);type=(?P<typecode>a|i|d)$,';

    /**
     * New instance
     *
     * @param int $type
     */
    public function __construct($type)
    {
        $this->type = $this->filterType($type);
    }

    /**
     * filter and validate the extension to use
     *
     * @param int $type
     *
     * @return int
     */
    protected function filterType($type)
    {
        if (!isset(static::$typecodeList[$type])) {
            throw new InvalidArgumentException('invalid code type');
        }

        return $type;
    }

    /**
     * Modify a URI part
     *
     * @param string $str the URI part string representation
     *
     * @return string the modified URI part string representation
     */
    protected function modifyPath($path)
    {
        if (preg_match(self::$typeRegex, $path, $matches)) {
            $path = $matches['basename'];
        }

        $extension = trim(self::$typecodeList[$this->type]);
        if ('' !== $extension) {
            $extension = ';type='.$extension;
        }

        return $path.$extension;
    }
}
