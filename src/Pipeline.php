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

use League\Uri\Schemes\Uri;
use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * A class to ease applying multiple modification
 * on a URI object based on the pipeline pattern
 * This class is based on league.pipeline
 *
 * @package League.uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   4.0.0
 */
class Pipeline extends ManipulateUri
{
    /**
     * @var callable[]
     */
    protected $modifiers;

    /**
     * New instance
     *
     * @param callable[] $modifiers
     */
    public function __construct($modifiers = [])
    {
        $this->modifiers = $this->filterCallable(...$modifiers);
    }

    /**
     * Create a new pipeline with an appended modifier.
     *
     * @param callable $modifier
     *
     * @return static
     */
    public function pipe(callable $modifier): self
    {
        $clone = clone $this;
        $clone->modifiers[] = $modifier;

        return $clone;
    }

    /**
     * Iteratively apply the modifiers to a URI object
     *
     * @param Uri|UriInterface $uri
     *
     * @return Uri|UriInterface
     *
     * @see Pipeline::__invoke
     */
    public function process($uri)
    {
        return $this->__invoke($uri);
    }

    /**
     * @inheritdoc
     */
    public function __invoke($uri)
    {
        $this->assertUriObject($uri);
        $uri_class = get_class($uri);

        $reducer = function ($uri, callable $modifier) use ($uri_class) {
            $uri = $modifier($uri);
            if (!is_object($uri) || $uri_class !== get_class($uri)) {
                throw new RuntimeException('The returned value is not of the same class as the submitted URI object');
            }

            return $uri;
        };

        return array_reduce($this->modifiers, $reducer, $uri);
    }
}
