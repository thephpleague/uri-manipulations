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
declare(strict_types=1);

namespace League\Uri\Modifiers;

/**
 * A class to ease applying multiple modification
 * on a URI object based on the pipeline pattern
 * This class is based on league.pipeline
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   1.0.0
 */
class CallableUriMiddleware extends AbstractUriMiddleware
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * New instance
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @inheritdoc
     */
    public function process($uri)
    {
        $this->assertUriObject($uri);
        $uri_class = get_class($uri);
        $new_uri = ($this->callable)($uri);
        if (!is_object($new_uri) || $uri_class !== get_class($new_uri)) {
            throw Exception::fromInvalidClass($new_uri, $uri_class);
        }

        return $new_uri;
    }
}
