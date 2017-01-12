<?php

namespace LeagueTest\Uri\Modifiers;

use InvalidArgumentException;
use League\Uri\Modifiers\CallableUriMiddleware;
use League\Uri\Modifiers\Exception;
use League\Uri\Modifiers\Pipeline;
use League\Uri\Modifiers\RemoveDotSegments;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group pipeline
 */
class PipelineTest extends TestCase
{
    public function testPipe()
    {
        $pipeline = new Pipeline([new RemoveDotSegments()]);
        $alt = $pipeline->pipe(new RemoveDotSegments());
        $this->assertInstanceOf(Pipeline::class, $alt);
        $this->assertNotEquals($alt, $pipeline);
    }

    public function testInvoke()
    {
        $uri = Http::createFromString('http://www.example.com/path/../to/the/./sky.php?kingkong=toto&foo=bar+baz#doc3');
        $pipeline = new Pipeline([new RemoveDotSegments()]);
        $newUri = $pipeline->process($uri);
        $this->assertInstanceOf(Http::class, $newUri);
        $this->assertSame('/to/the/sky.php', $newUri->getPath());
    }

    public function testInvokeThrowInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = 'http://www.example.com/path/../to/the/./sky.php?kingkong=toto&foo=bar+baz#doc3';
        $pipeline = new Pipeline([new RemoveDotSegments()]);
        $newUri = $pipeline->process($uri);
    }

    public function testCallableUriMiddleware()
    {
        $uri = Http::createFromString('http://example.com');
        $pipeline = (new Pipeline())->pipe(new CallableUriMiddleware(function ($uri) {
            return $uri;
        }));

        $this->assertEquals($pipeline->process($uri), $uri);
    }

    public function testInvokeThrowRuntimeException()
    {
        $this->expectException(Exception::class);
        $modifier = function (Http $uri) {
            return true;
        };

        $uri = Http::createFromString('http://www.example.com');
        Pipeline::createFromCallables([$modifier])->process($uri);
    }
}
