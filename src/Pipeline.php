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
     *
     * @throws InvalidArgumentException
     */
    public function __construct($modifiers = [])
    {
        foreach ($modifiers as $modifier) {
            if (false === is_callable($modifier)) {
                throw new InvalidArgumentException('All submitted modifiers should be callable');
            }
        }
        $this->modifiers = $modifiers;
    }

    /**
     * Create a new pipeline with an appended modifier.
     *
     * @param callable $modifier
     *
     * @return static
     */
    public function pipe(callable $modifier)
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
        $submittedUriClass = get_class($uri);
        foreach ($this->modifiers as $modifier) {
            $uri = call_user_func($modifier, $uri);
            if (!is_object($uri) || $submittedUriClass !== get_class($uri)) {
                throw new RuntimeException(
                    'The returned value is not of the same class as the submitted URI object'
                );
            }
        }

        return $uri;
    }
}
