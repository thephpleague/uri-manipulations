<?php

namespace LeagueTest\Uri\Modifiers;

use GuzzleHttp\Psr7\Uri as GuzzleUri;
use InvalidArgumentException;
use League\Uri\Components\DataPath;
use League\Uri\Components\Path;
use League\Uri\Modifiers\AddBasePath;
use League\Uri\Modifiers\AddLeadingSlash;
use League\Uri\Modifiers\AddTrailingSlash;
use League\Uri\Modifiers\AppendSegment;
use League\Uri\Modifiers\Basename;
use League\Uri\Modifiers\DataUriParameters;
use League\Uri\Modifiers\DataUriToAscii;
use League\Uri\Modifiers\DataUriToBinary;
use League\Uri\Modifiers\Dirname;
use League\Uri\Modifiers\Extension;
use League\Uri\Modifiers\PrependSegment;
use League\Uri\Modifiers\RemoveBasePath;
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
        $this->assertSame((string) $binary, (string) $modifier->process($ascii));
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
        $this->assertSame((string) $ascii, (string) $modifier->process($binary));
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
        $this->assertSame('text/plain;coco=chanel,Bonjour%20le%20monde!', (string) $modifier->process($uri)->getPath());
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
        $this->assertSame($append, $modifier->process($this->uri)->getPath());
    }

    /**
     * @dataProvider validAppendPathProvider
     */
    public function testAppendProcessWithRelativePath($uri, $segment, $expected)
    {
        $modifier = new AppendSegment($segment);
        $this->assertSame($expected, (string) $modifier->process(Http::createFromString($uri)));
    }
    public function validAppendPathProvider()
    {
        return [
            'uri with trailing slash' => [
                'uri' => 'http://www.example.com/report/',
                'segment' => 'new-segment',
                'expected' => 'http://www.example.com/report/new-segment',
            ],
            'uri with path without trailing slash' => [
                'uri' => 'http://www.example.com/report',
                'segment' => 'new-segment',
                'expected' => 'http://www.example.com/report/new-segment',
            ],
            'uri with absolute path' => [
                'uri' => 'http://www.example.com/',
                'segment' => 'new-segment',
                'expected' => 'http://www.example.com/new-segment',
            ],
            'uri with empty path' => [
                'uri' => 'http://www.example.com',
                'segment' => 'new-segment',
                'expected' => 'http://www.example.com/new-segment',
            ],
        ];
    }

    /**
     * @dataProvider validBasenameProvider
     */
    public function testBasename($path, $uri, $expected)
    {
        $modifier = new Basename($path);
        $this->assertSame($expected, (string) $modifier->process(new GuzzleUri($uri)));
    }

    public function validBasenameProvider()
    {
        return [
            ['baz', 'http://example.com', 'http://example.com/baz'],
            ['baz', 'http://example.com/foo/bar', 'http://example.com/foo/baz'],
            ['baz', 'http://example.com/foo/', 'http://example.com/foo/baz'],
            ['baz', 'http://example.com/foo', 'http://example.com/baz'],
        ];
    }

    public function testBasenameThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Basename('foo/baz');
    }

    /**
     * @dataProvider validDirnameProvider
     */
    public function testDirname($path, $uri, $expected)
    {
        $modifier = new Dirname($path);
        $this->assertSame($expected, (string) $modifier->process(new GuzzleUri($uri)));
    }

    public function validDirnameProvider()
    {
        return [
            ['baz', 'http://example.com', 'http://example.com/baz/'],
            ['baz/', 'http://example.com', 'http://example.com/baz/'],
            ['baz', 'http://example.com/foo', 'http://example.com/baz/foo'],
            ['baz', 'http://example.com/foo/yes', 'http://example.com/baz/yes'],
        ];
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
        $this->assertSame($prepend, $modifier->process($this->uri)->getPath());
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
        $this->assertSame($replace, $modifier->process($this->uri)->getPath());
    }

    public function validPathProvider()
    {
        return [
            ['toto', 2, '/path/to/the/sky.php/toto', '/toto/path/to/the/sky.php', '/path/to/toto/sky.php'],
            ['le blanc', 2, '/path/to/the/sky.php/le%20blanc', '/le%20blanc/path/to/the/sky.php', '/path/to/le%20blanc/sky.php'],
        ];
    }

    /**
     * @dataProvider addBasePathProvider
     */
    public function testAddBasePath($basepath, $expected)
    {
        $modifier = new AddBasePath($basepath);
        $this->assertSame($expected, $modifier->process($this->uri)->getPath());
    }

    public function addBasePathProvider()
    {
        return [
            ['/', '/path/to/the/sky.php'],
            ['', '/path/to/the/sky.php'],
            ['/path/to', '/path/to/the/sky.php'],
            ['/route/to', '/route/to/path/to/the/sky.php'],
        ];
    }

    public function testAddBasePathWithRelativePath()
    {
        $uri = Http::createFromString('base/path');
        $modifier = new AddBasePath('/base/path');
        $this->assertSame('/base/path', $modifier->process($uri)->getPath());
    }

    /**
     * @dataProvider removeBasePathProvider
     */
    public function testRemoveBasePath($basepath, $expected)
    {
        $modifier = new RemoveBasePath($basepath);
        $this->assertSame($expected, $modifier->process($this->uri)->getPath());
    }

    public function removeBasePathProvider()
    {
        return [
            ['/', '/path/to/the/sky.php'],
            ['', '/path/to/the/sky.php'],
            ['/path/to', '/the/sky.php'],
            ['/route/to', '/path/to/the/sky.php'],
        ];
    }

    public function testRemoveBasePathWithRelativePath()
    {
        $uri = Http::createFromString('base/path');
        $modifier = new RemoveBasePath('/base/path');
        $this->assertSame('/', $modifier->process($uri)->getPath());
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

        $this->assertSame($expected, $modifier->process($this->uri)->getPath());
    }

    public function validWithoutSegmentsProvider()
    {
        return [
            [[1], '/path/the/sky.php'],
        ];
    }

    /**
     * @dataProvider invalidRemoveSegmentsParameters
     */
    public function testRemoveSegmentsFailedConstructor($params)
    {
        $this->expectException(InvalidArgumentException::class);
        new RemoveSegments($params);
    }

    public function invalidRemoveSegmentsParameters()
    {
        return [
            'array contains float' => [[1, 2, '3.1']],
        ];
    }

    public function testWithoutDotSegmentsProcess()
    {
        $uri = Http::createFromString(
            'http://www.example.com/path/../to/the/./sky.php?kingkong=toto&foo=bar+baz#doc3'
        );
        $modifier = new RemoveDotSegments();
        $this->assertSame('/to/the/sky.php', $modifier->process($uri)->getPath());
    }

    public function testWithoutEmptySegmentsProcess()
    {
        $uri = Http::createFromString(
            'http://www.example.com/path///to/the//sky.php?kingkong=toto&foo=bar+baz#doc3'
        );
        $modifier = new RemoveEmptySegments();
        $this->assertSame('/path/to/the/sky.php', $modifier->process($uri)->getPath());
    }

    public function testWithoutTrailingSlashProcess()
    {
        $uri = Http::createFromString('http://www.example.com/');
        $modifier = new RemoveTrailingSlash();
        $this->assertSame('', $modifier->process($uri)->getPath());
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

        $this->assertSame($expected, $modifier->process($this->uri)->getPath());
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
        $this->assertSame('/path/to/the/sky.php/', $modifier->process($this->uri)->getPath());
    }

    public function testWithoutLeadingSlashProcess()
    {
        $modifier = new RemoveLeadingSlash();
        $uri = Http::createFromString('/foo/bar?q=b#h');

        $this->assertSame('foo/bar?q=b#h', $modifier->process($uri)->__toString());
    }

    public function testWithLeadingSlashProcess()
    {
        $modifier = new AddLeadingSlash();
        $uri = Http::createFromString('foo/bar?q=b#h');

        $this->assertSame('/foo/bar?q=b#h', $modifier->process($uri)->__toString());
    }

    public function testAppendProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AppendSegment(''))->process('http://www.example.com');
    }


    public function testPrependProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new PrependSegment(''))->process('http://www.example.com');
    }

    public function testWithoutDotSegmentsProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RemoveDotSegments())->process('http://www.example.com');
    }

    public function testReplaceSegmentConstructorFailed2()
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceSegment(2, "whyno\0t");
    }

    public function testWithoutLeadingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RemoveLeadingSlash())->process('http://www.example.com');
    }

    public function testWithLeadingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AddLeadingSlash())->process('http://www.example.com');
    }

    public function testWithoutTrailingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AddTrailingSlash())->process('http://www.example.com');
    }

    public function testWithTrailingSlashProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AddTrailingSlash())->process('http://www.example.com');
    }

    public function testExtensionProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new Extension('to/to');
    }

    public function testWithoutEmptySegmentsProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RemoveEmptySegments())->process('http://www.example.com');
    }
}
