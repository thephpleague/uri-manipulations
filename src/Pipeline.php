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
class Pipeline implements UriMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * @var callable[]
     */
    protected $modifiers;

    /**
     * New instance
     *
     * @param callable[] $callables
     */
    public static function createFromCallables($callables = [])
    {
        $pipeline = new static();
        $pipeline->modifiers = (function (callable ...$callables) {
            return array_map(function (callable $value) {
                return new CallableUriMiddleware($value);
            }, $callables);
        })(...$callables);

        return $pipeline;
    }

    /**
     * New instance
     *
     * @param UriMiddlewareInterface[] $modifiers
     */
    public function __construct($modifiers = [])
    {
        $this->modifiers = (function (UriMiddlewareInterface ...$middlewares) {
            return $middlewares;
        })(...$modifiers);
    }

    /**
     * Create a new pipeline with an appended modifier.
     *
     * @param UriMiddlewareInterface $modifier
     *
     * @return static
     */
    public function pipe(UriMiddlewareInterface $modifier): self
    {
        $clone = clone $this;
        $clone->modifiers[] = $modifier;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    protected function execute($uri)
    {
        $reducer = function ($uri, UriMiddlewareInterface $modifier) {
            return $modifier->process($uri);
        };

        return array_reduce($this->modifiers, $reducer, $uri);
    }
}
