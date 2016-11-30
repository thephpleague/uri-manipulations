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
use League\Uri\Components\Fragment;
use League\Uri\Components\Host;
use League\Uri\Components\Path;
use League\Uri\Components\Query;
use League\Uri\Components\UserInfo;
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
    const RFC3986 = Component::RFC3986;

    const RFC3987 = Component::RFC3987;

    /**
     * host encoding property
     *
     * @var string
     */
    protected $enc_type = self::RFC3986;

    /**
     * query separator property
     *
     * @var string
     */
    protected $query_separator = '&';

    /**
     * Should the query component be preserved
     *
     * @var bool
     */
    protected $preserve_query = false;

    /**
     * Should the fragment component string be preserved
     *
     * @var bool
     */
    protected $preserve_fragment = false;

    /**
     * Formatting encoding type
     *
     * @param string $enc_type a predefined constant value
     */
    public function setEncoding($enc_type)
    {
        if (!in_array($enc_type, [self::RFC3987, self::RFC3986])) {
            throw new InvalidArgumentException('Unknown encoding rule');
        }

        $this->enc_type = $enc_type;
    }

    /**
     * Query separator setter
     *
     * @param string $separator
     */
    public function setQuerySeparator($separator)
    {
        $separator = filter_var($separator, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        $this->query_separator = trim($separator);
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
        $this->preserve_query = (bool) $status;
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
        $this->preserve_fragment = (bool) $status;
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
        if ($input instanceof Query) {
            return Query::build($input->getPairs(), $this->query_separator, $this->enc_type);
        }

        if ($input instanceof Component) {
            return $input->getContent($this->enc_type);
        }

        if ($input instanceof Uri || $input instanceof UriInterface) {
            return $this->formatUri($input);
        }

        throw new InvalidArgumentException(
            'input must be an URI object or a League URI Component object'
        );
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

        $user_info = (new UserInfo())->withContent($uri->getUserInfo())->getContent($this->enc_type);
        if ('' !== $user_info) {
            $user_info .= '@';
        }

        $host = (new Host($uri->getHost()))->getContent($this->enc_type);

        $port = $uri->getPort();
        if ('' != $port) {
            $port = ':'.$port;
        }

        $authority = $user_info.$host.$port;
        if ('' != $authority) {
            $authority = '//'.$authority;
        }

        $path = (new Path($uri->getPath()))->getContent($this->enc_type);

        $query = Query::build(Query::parse($uri->getQuery()), $this->query_separator, $this->enc_type);
        if ($this->preserve_query || '' != $query) {
            $query = '?'.$query;
        }

        $fragment = (new Fragment($uri->getFragment()))->getContent($this->enc_type);
        if ($this->preserve_fragment || '' != $fragment) {
            $fragment = '#'.$fragment;
        }

        return $scheme.$authority.$path.$query.$fragment;
    }
}
