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

use InvalidArgumentException;
use League\Uri;
use League\Uri\Components\ComponentInterface;
use League\Uri\Components\EncodingInterface;
use League\Uri\Components\Fragment;
use League\Uri\Components\Host;
use League\Uri\Components\Path;
use League\Uri\Components\Query;
use League\Uri\Components\UserInfo;
use League\Uri\Interfaces\Uri as LeagueUriInterface;
use Psr\Http\Message\UriInterface;

/**
 * A class to manipulate URI and URI components output
 *
 * @package    League\Uri
 * @subpackage League\Uri\Modifiers
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
class Formatter implements EncodingInterface
{
    /**
     * host encoding property
     *
     * @var int
     */
    protected $enc_type = self::RFC3986_ENCODING;

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
     * @param int $enc_type a predefined constant value
     */
    public function setEncoding(int $enc_type)
    {
        static $enc_type_list;
        if (null === $enc_type_list) {
            $enc_type_list = [
                self::RFC1738_ENCODING => 1,
                self::RFC3986_ENCODING => 1,
                self::RFC3987_ENCODING => 1,
                self::NO_ENCODING => 1,
            ];
        }

        if (!isset($enc_type_list[$enc_type])) {
            throw new InvalidArgumentException(sprintf('Unsupported or Unknown Encoding: %s', $enc_type));
        }

        $this->enc_type = $enc_type;
    }

    /**
     * Query separator setter
     *
     * @param string $separator
     */
    public function setQuerySeparator(string $separator)
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
    public function preserveQuery(bool $status)
    {
        $this->preserve_query = $status;
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
    public function preserveFragment(bool $status)
    {
        $this->preserve_fragment = $status;
    }

    /**
     * Format an Uri object
     *
     * Format an object according to the formatter properties.
     * The object must implement one of the following interface:
     * <ul>
     * <li>League\Uri\Interfaces\Uri
     * <li>League\Uri\Interfaces\UriPartInterface
     * <li>Psr\Http\Message\UriInterface
     * </ul>
     *
     * @param mixed $input
     *
     * @return string
     */
    public function __invoke($input)
    {
        if ($input instanceof Query) {
            return Uri\build_query($input->getPairs(), $this->query_separator, $this->enc_type);
        }

        if ($input instanceof ComponentInterface) {
            return $input->getContent($this->enc_type);
        }

        if ($input instanceof LeagueUriInterface || $input instanceof UriInterface) {
            return $this->formatUri($input);
        }

        throw new InvalidArgumentException('input must be an URI object or a League URI Component object');
    }

    /**
     * Format an Uri object
     *
     * @see __invoke()
     *
     * @param mixed $input
     *
     * @return string
     */
    public function format($input)
    {
        return $this->__invoke($input);
    }

    /**
     * Format an Uri according to the Formatter properties
     *
     * @param LeagueUriInterface|UriInterface $uri
     *
     * @return string
     */
    protected function formatUri($uri): string
    {
        $scheme = $uri->getScheme();
        if ('' != $scheme) {
            $scheme = $scheme.':';
        }

        $authority = null;
        $host = $uri->getHost();
        if ('' != $host) {
            $user_info = $uri->getUserInfo();
            if ('' != $user_info) {
                $authority .= (new UserInfo())->withContent($user_info)->getContent($this->enc_type).'@';
            }
            $authority .= (new Host($host))->getContent($this->enc_type);
            $port = $uri->getPort();
            if (null !== $port) {
                $authority .= ':'.$port;
            }
        }

        if (null !== $authority) {
            $authority = '//'.$authority;
        }

        $path = (new Path($uri->getPath()))->getContent($this->enc_type);
        if (null !== $authority && '' !== $path && '/' !== $path[0]) {
            $path = '/'.$path;
        }

        $query = $uri->getQuery();
        if ('' != $query || $this->preserve_query) {
            $query = '?'.Uri\build_query(Uri\parse_query($query), $this->query_separator, $this->enc_type);
        }

        $fragment = $uri->getFragment();
        if ('' != $fragment || $this->preserve_fragment) {
            $fragment = '#'.(new Fragment($fragment))->getContent($this->enc_type);
        }

        return $scheme.$authority.$path.$query.$fragment;
    }
}
