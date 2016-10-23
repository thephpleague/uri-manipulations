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
use League\Uri\Components\Port;
use League\Uri\Components\Query;
use League\Uri\Interfaces\Component;
use League\Uri\Interfaces\Uri;
use Psr\Http\Message\UriInterface;

/**
 * A class to manipulate URI and URI components output
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Formatter
{
    const HOST_AS_UNICODE = 1;

    const HOST_AS_ASCII   = 2;

    /**
     * host encoding property
     *
     * @var int
     */
    protected $hostEncoding = self::HOST_AS_UNICODE;

    /**
     * query separator property
     *
     * @var string
     */
    protected $querySeparator = '&';

    /**
     * Should the query component be preserved
     *
     * @var bool
     */
    protected $preserveQuery = false;

    /**
     * Should the fragment component string be preserved
     *
     * @var bool
     */
    protected $preserveFragment = false;

    /**
     * Host encoding setter
     *
     * @param int $encode a predefined constant value
     */
    public function setHostEncoding($encode)
    {
        if (!in_array($encode, [self::HOST_AS_UNICODE, self::HOST_AS_ASCII])) {
            throw new InvalidArgumentException('Unknown Host encoding rule');
        }
        $this->hostEncoding = $encode;
    }

    /**
     * Query separator setter
     *
     * @param string $separator
     */
    public function setQuerySeparator($separator)
    {
        $separator = filter_var($separator, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        $this->querySeparator = trim($separator);
    }

    /**
     * Whether we should preserve the Query component
     * regardless of its value.
     *
     * If set to true the query delimiter will be appended
     * to the URI regardless of the query string value
     *
     * @param bool $status
     */
    public function preserveQuery($status)
    {
        $this->preserveQuery = (bool) $status;
    }

    /**
     * Whether we should preserve the Fragment component
     * regardless of its value.
     *
     * If set to true the fragment delimiter will be appended
     * to the URI regardless of the query string value
     *
     * @param bool $status
     */
    public function preserveFragment($status)
    {
        $this->preserveFragment = (bool) $status;
    }

    /**
     * Format an object
     *
     * Format an object according to the formatter properties.
     * The object must implement one of the following interface:
     * <ul>
     * <li>League\Uri\Interfaces\Uri
     * <li>League\Uri\Interfaces\UriPartInterface
     * <li>Psr\Http\Message\UriInterface
     * </ul>
     *
     * @param Component|Uri|UriInterface $input
     *
     * @return string
     */
    public function __invoke($input)
    {
        if ($input instanceof Component) {
            return $this->formatUriPart($input);
        }

        if ($input instanceof Uri || $input instanceof UriInterface) {
            return $this->formatUri($input);
        }

        throw new InvalidArgumentException(
            'input must be an URI object or a League UriPartInterface object'
        );
    }

    /**
     * Format a UriPartInterface implemented object according to the Formatter properties
     *
     * @param Component $part
     *
     * @return string
     */
    protected function formatUriPart(Component $part)
    {
        if ($part instanceof Query) {
            return Query::build($part->getPairs(), $this->querySeparator);
        }

        if ($part instanceof Host) {
            return $this->formatHost($part);
        }

        return (string) $part;
    }

    /**
     * Format a Host according to the Formatter properties
     *
     * @param Host $host
     *
     * @return string
     */
    protected function formatHost(Host $host)
    {
        if (self::HOST_AS_ASCII === $this->hostEncoding) {
            return (string) $host->toAscii();
        }

        return (string) $host->toUnicode();
    }

    /**
     * Format an Uri according to the Formatter properties
     *
     * @param Uri|UriInterface $uri
     *
     * @return string
     */
    protected function formatUri($uri)
    {
        $scheme = $uri->getScheme();
        if ('' !== $scheme) {
            $scheme .= ':';
        }

        return $scheme
            .$this->formatAuthority($uri)
            .$uri->getPath()
            .$this->formatQuery($uri)
            .$this->formatFragment($uri)
        ;
    }

    /**
     * Format a URI authority according to the Formatter properties
     *
     * @param Uri|UriInterface $uri
     *
     * @return string
     */
    protected function formatAuthority($uri)
    {
        if ('' === $uri->getAuthority()) {
            return '';
        }

        $userInfo = $uri->getUserInfo();
        if ('' !== $userInfo) {
            $userInfo .= '@';
        }

        return '//'
            .$userInfo
            .$this->formatHost(new Host($uri->getHost()))
            .$this->formatPort($uri->getPort())
        ;
    }

    /**
     * Format a URI port component according to the Formatter properties
     *
     * @param null|int $port
     *
     * @return string
     */
    protected function formatPort($port)
    {
        if (null !== $port) {
            return ':'.$port;
        }

        return '';
    }

    /**
     * Format a URI Query component according to the Formatter properties
     *
     * @param Uri|UriInterface $uri
     *
     * @return string
     */
    protected function formatQuery($uri)
    {
        $query = $this->formatUriPart(new Query($uri->getQuery()));
        if ($this->preserveQuery || '' !== $query) {
            $query = '?'.$query;
        }

        return $query;
    }

    /**
     * Format a URI Fragment component according to the Formatter properties
     *
     * @param Uri|UriInterface $uri
     *
     * @return string
     */
    protected function formatFragment($uri)
    {
        $fragment = $uri->getFragment();
        if ($this->preserveFragment || '' != $fragment) {
            $fragment = '#'.$fragment;
        }

        return $fragment;
    }
}
