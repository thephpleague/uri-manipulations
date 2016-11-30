<?php

namespace LeagueTest\Uri\Modifiers;

use InvalidArgumentException;
use League\Uri\Components\DataPath;
use League\Uri\Components\Path;
use League\Uri\Modifiers\AddLeadingSlash;
use League\Uri\Modifiers\AddTrailingSlash;
use League\Uri\Modifiers\AppendSegment;
use League\Uri\Modifiers\DataUriParameters;
use League\Uri\Modifiers\DataUriToAscii;
use League\Uri\Modifiers\DataUriToBinary;
use League\Uri\Modifiers\Extension;
use League\Uri\Modifiers\FilterSegments;
use League\Uri\Modifiers\PrependSegment;
use League\Uri\Modifiers\RemoveDotSegments;
use League\Uri\Modifiers\RemoveEmptySegments;
use League\Uri\Modifiers\RemoveLeadingSlash;
use League\Uri\Modifiers\RemoveSegments;
use League\Uri\Modifiers\RemoveTrailingSlash;
use League\Uri\Modifiers\ReplaceSegment;
use League\Uri\Schemes\Data;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group path
 * @group modifier
 */
class PathManipulatorTest extends TestCase
{
    /**
     * @var Http
     */
    private $uri;

    protected function setUp()
    {
        $this->uri = Http::createFromString(
            'http://www.example.com/path/to/the/sky.php?kingkong=toto&foo=bar+baz#doc3'
        );
    }

    /**
     * @dataProvider fileProvider
     *
     * @param DataUri $binary
     * @param DataUri $ascii
     */
    public function testToBinary($binary, $ascii)
    {
        $modifier = new DataUriToBinary();
        $this->assertSame((string) $binary, (string) $modifier($ascii));
    }

    /**
     * @dataProvider fileProvider
     *
     * @param DataUri $binary
     * @param DataUri $ascii
     */
    public function testToAscii($binary, $ascii)
    {
        $modifier = new DataUriToAscii();
        $this->assertSame((string) $ascii, (string) $modifier($binary));
    }

    public function fileProvider()
    {
        $textPath = new DataPath('text/plain;charset=us-ascii,Bonjour%20le%20monde%21');
        $binPath = DataPath::createFromPath(__DIR__.'/data/red-nose.gif');

        $ascii = Data::createFromString('data:text/plain;charset=us-ascii,Bonjour%20le%20monde%21');
        $binary = Data::createFromString('data:'.$textPath->toBinary());

        $pathBin = Data::createFromPath(__DIR__.'/data/red-nose.gif');
        $pathAscii = Data::createFromString('data:'.$binPath->toAscii());

        return [
            [$pathBin, $pathAscii],
            [$binary, $ascii],
        ];
    }

    public function testDataUriWithParameters()
    {
        $modifier = new DataUriParameters('coco=chanel');
        $uri = Data::createFromString('data:text/plain;charset=us-ascii,Bonjour%20le%20monde!');
        $this->assertSame('text/plain;coco=chanel,Bonjour%20le%20monde!', (string) $modifier($uri)->getPath());
    }

    /**
     * @dataProvider validPathProvider
     *
     * @param string $segment
     * @param int    $key
     * @param string $append
     * @param string $prepend
     * @param string $replace
     */
    public function testAppendProcess($segment, $key, $append, $prepend, $replace)
    {
        $modifier = new AppendSegment($segment);
        $this->assertSame($append, $modifier($this->uri)->getPath());
    }

    /**
     * @dataProvider validPathProvider
     *
     * @param string $segment
     * @param int    $key
     * @param string $append
     * @param string $prepend
     * @param string $replace
     */
    public function testPrependProcess($segment, $key, $append, $prepend, $replace)
    {
        $modifier = new PrependSegment($segment);
        $this->assertSame($prepend, $modifier($this->uri)->getPath());
    }

    /**
     * @dataProvider validPathProvider
     *
     * @param string $segment
     * @param int    $key
     * @param string $append
     * @param string $prepend
     * @param string $replace
     */
    public function testReplaceSegmentProcess($segment, $key, $append, $prepend, $replace)
    {
        $modifier = new ReplaceSegment($key, $segment);
        $this->assertSame($replace, $modifier($this->uri)->getPath());
    }

    public function validPathProvider()
    {
        return [
            ['toto', 2, '/path/to/the/sky.php/toto', '/toto/path/to/the/sky.php', '/path/to/toto/sky.php'],
            ['le blanc', 2, '/path/to/the/sky.php/le%20blanc', '/le%20blanc/path/to/the/sky.php', '/path/to/le%20blanc/sky.php'],
        ];
    }

    /**
     * @dataProvider validWithoutSegmentsProvider
     *
     * @param array  $keys
     * @param string $expected
     */
    public function testWithoutSegments($keys, $expected)
    {
        $modifier = new RemoveSegments($keys);

        $this->assertSame($expected, $modifier($this->uri)->getPath());
    }

    public function validWithoutSegmentsProvider()
    {
        return [
            [[1], '/path/the/sky.php'],
        ];
    }

    public function testFilterSegments()
    {
        $modifier = new FilterSegments(function ($value) {
            return $value > 0 && $value < 2;
        }, ARRAY_FILTER_USE_KEY);

        $this->assertSame('/to', $modifier($this->uri)->getPath());
    }

    public function testWithoutDotSegmentsProcess()
    {
        $uri = Http::createFromString(
            'http://www.example.com/path/../to/the/./sky.php?kingkong=toto&foo=bar+baz#doc3'
        );
        $modifier = new RemoveDotSegments();
        $this->assertSame('/to/the/sky.php', $modifier($uri)->getPath());
    }

    public function testWithoutEmptySegmentsProcess()
    {
        $uri = Http::createFromString(
            'http://www.example.com/path///to/the//sky.php?kingkong=toto&foo=bar+baz#doc3'
        );
        $modifier = new RemoveEmptySegments();
        $this->assertSame('/path/to/the/sky.php', $modifier($uri)->getPath());
    }

    public function testWithoutTrailingSlashProcess()
    {
        $uri = Http::createFromString('http://www.example.com/');
        $modifier = new RemoveTrailingSlash();
        $this->assertSame('', $modifier($uri)->getPath());
    }

    /**
     * @dataProvider validExtensionProvider
     *
     * @param string $extension
     * @param string $expected
     */
    public function testExtensionProcess($extension, $expected)
    {
        $modifier = new Extension($extension);

        $this->assertSame($expected, $modifier($this->uri)->getPath());
    }

    public function validExtensionProvider()
    {
        return [
            ['csv', '/path/to/the/sky.csv'],
            ['', '/path/to/the/sky'],
        ];
    }

    public function testWithTrailingSlashProcess()
    {
        $modifier = new AddTrailingSlash();
        $this->assertSame('/path/to/the/sky.php/', $modifier($this->uri)->getPath());
    }

    public function testWithoutLeadingSlashProcess()
    {
        $modifier = new RemoveLeadingSlash();
        $uri = Http::createFromString('/foo/bar?q=b#h');

        $this->assertSame('foo/bar?q=b#h', $modifier($uri)->__toString());
    }

    public function testWithLeadingSlashProcess()
    {
        $modifier = new AddLeadingSlash();
        $uri = Http::createFromString('foo/bar?q=b#h');

        $this->assertSame('/foo/bar?q=b#h', $modifier($uri)->__toString());
    }

    public function testAppendProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AppendSegment(''))->__invoke('http://www.example.com');
    }

    public function testAppendConstructorFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new AppendSegment(new Path('whynot'));
    }

    public function testPrependProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new PrependSegment(''))->__invoke('http://www.example.com');
    }

    public function testPrependConstructorFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new PrependSegment(new Path('whynot'));
    }

    public function testWithoutDotSegmentsProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RemoveDotSegments())->__invoke('http://www.example.com');
    }

    public function testReplaceSegmentConstructorFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceSegment(2, new Path('whynot'));
    }

    public function testReplaceSegmentConstructorFailed2()
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceSegment(2, "whyno\0t");
    }

    public function testWithoutLeadingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RemoveLeadingSlash())->__invoke('http://www.example.com');
    }

    public function testWithLeadingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AddLeadingSlash())->__invoke('http://www.example.com');
    }

    public function testWithoutTrailingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AddTrailingSlash())->__invoke('http://www.example.com');
    }

    public function testWithTrailingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AddTrailingSlash())->__invoke('http://www.example.com');
    }

    public function testExtensionProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new Extension('to/to');
    }

    public function testWithoutEmptySegmentsProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RemoveEmptySegments())->__invoke('http://www.example.com');
    }
}
