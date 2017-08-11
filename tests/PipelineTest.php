<?php

namespace LeagueTest\Uri;

use GuzzleHttp\Psr7;
use InvalidArgumentException;
use League\Uri\Modifiers\CallableAdapter;
use League\Uri\Modifiers\Pipeline;
use League\Uri\Modifiers\RemoveDotSegments;
use League\Uri\Schemes\Ftp;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group pipeline
 * @coversDefaultClass League\Uri\Modifiers\Pipeline
 */
class PipelineTest extends TestCase
{

    /**
     * @covers ::pipe
     * @covers ::__construct
     */
    public function testPipe()
    {
        $pipeline = new Pipeline([new RemoveDotSegments()]);
        $alt = $pipeline->pipe(new RemoveDotSegments());
        $this->assertInstanceOf(Pipeline::class, $alt);
        $this->assertNotEquals($alt, $pipeline);
    }

    /**
     * @covers ::execute
     * @covers League\Uri\Modifiers\UriMiddlewareTrait<extended>
     */
    public function testInvoke()
    {
        $uri = Http::createFromString('http://www.example.com/path/../to/the/./sky.php?kingkong=toto&foo=bar+baz#doc3');
        $pipeline = new Pipeline([new RemoveDotSegments()]);
        $newUri = $pipeline->__invoke($uri);
        $this->assertInstanceOf(Http::class, $newUri);
        $this->assertSame('/to/the/sky.php', $newUri->getPath());
    }

    /**
     * @covers ::execute
     * @covers League\Uri\Modifiers\Exception
     * @covers League\Uri\Modifiers\UriMiddlewareTrait<extended>
     */
    public function testInvokeThrowInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = 'http://www.example.com/path/../to/the/./sky.php?kingkong=toto&foo=bar+baz#doc3';
        $pipeline = new Pipeline([new RemoveDotSegments()]);
        $newUri = $pipeline->process($uri);
    }

    /**
     * @covers League\Uri\Modifiers\CallableAdapter
     */
    public function testCallableAdapter()
    {
        $uri = Http::createFromString('http://example.com');
        $pipeline = (new Pipeline())->pipe(new CallableAdapter(function ($uri) {
            return $uri;
        }));

        $this->assertEquals($pipeline->process($uri), $uri);
    }

    /**
     * @covers League\Uri\Modifiers\CallableAdapter
     * @covers League\Uri\Modifiers\Exception
     */
    public function testCallableAdapterTriggerException()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = Http::createFromString('http://example.com');
        $pipeline = (new Pipeline())->pipe(new CallableAdapter(function ($uri) {
            return true;
        }));

        $pipeline->process($uri);
    }

    /**
     * @covers ::execute
     * @covers League\Uri\Modifiers\UriMiddlewareTrait::process
     */
    public function testProcessTriggerException()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = Ftp::createFromString('ftp://example.com');
        $pipeline = (new Pipeline())->pipe(new CallableAdapter(function ($uri) {
            return Psr7\uri_for('http://google.com');
        }));

        $pipeline->process($uri);
    }
}
