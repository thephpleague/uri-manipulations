<?php

namespace LeagueTest\Uri\Modifiers;

use InvalidArgumentException;
use League\Uri\Modifiers\CallableAdapter;
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

    public function testCallableAdapter()
    {
        $uri = Http::createFromString('http://example.com');
        $pipeline = (new Pipeline())->pipe(new CallableAdapter(function ($uri) {
            return $uri;
        }));

        $this->assertEquals($pipeline->process($uri), $uri);
    }

    public function testCallableAdapterTriggerException()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = Http::createFromString('http://example.com');
        $pipeline = (new Pipeline())->pipe(new CallableAdapter(function ($uri) {
            return true;
        }));

        $pipeline->process($uri);
    }
}
