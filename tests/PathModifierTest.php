<?php

namespace LeagueTest\Uri;

use GuzzleHttp\Psr7;
use InvalidArgumentException;
use League\Uri;
use League\Uri\Components\DataPath;
use League\Uri\Components\Path;
use League\Uri\Schemes\Data;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group path
 * @group modifier
 */
class PathModifierTest extends TestCase
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
     * @covers \League\Uri\path_to_binary
     * @covers \League\Uri\Modifiers\DataUriToBinary
     *
     * @dataProvider fileProvider
     *
     * @param Data $binary
     * @param Data $ascii
     */
    public function testToBinary(Data $binary, Data $ascii)
    {
        $this->assertSame((string) $binary, (string) Uri\path_to_binary($ascii));
    }

    /**
     * @covers \League\Uri\path_to_ascii
     * @covers \League\Uri\Modifiers\DataUriToAscii
     *
     * @dataProvider fileProvider
     *
     * @param Data $binary
     * @param Data $ascii
     */
    public function testToAscii(Data $binary, Data $ascii)
    {
        $this->assertSame((string) $ascii, (string) Uri\path_to_ascii($binary));
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

    /**
     * @covers \League\Uri\replace_data_uri_parameters
     * @covers \League\Uri\Modifiers\DataUriParameters
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     */
    public function testDataUriWithParameters()
    {
        $uri = Data::createFromString('data:text/plain;charset=us-ascii,Bonjour%20le%20monde!');
        $this->assertSame(
            'text/plain;coco=chanel,Bonjour%20le%20monde!',
            Uri\replace_data_uri_parameters($uri, 'coco=chanel')->getPath()
        );
    }

    /**
     * @covers \League\Uri\append_path
     * @covers \League\Uri\Modifiers\AppendSegment
     * @covers \League\Uri\Modifiers\PathMiddlewareTrait<extended>
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @dataProvider validPathProvider
     *
     * @param string $segment
     * @param int    $key
     * @param string $append
     * @param string $prepend
     * @param string $replace
     */
    public function testAppendProcess(string $segment, int $key, string $append, string $prepend, string $replace)
    {
        $this->assertSame($append, Uri\append_path($this->uri, $segment)->getPath());
    }

    /**
     * @covers \League\Uri\append_path
     * @covers \League\Uri\Modifiers\AppendSegment
     * @covers \League\Uri\Modifiers\PathMiddlewareTrait<extended>
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @dataProvider validAppendPathProvider
     *
     * @param string $uri
     * @param string $segment
     * @param string $expected
     */
    public function testAppendProcessWithRelativePath(string $uri, string $segment, string $expected)
    {
        $this->assertSame($expected, (string) Uri\append_path(Http::createFromString($uri), $segment));
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
     * @covers \League\Uri\replace_basename
     * @covers \League\Uri\Modifiers\Basename
     *
     * @dataProvider validBasenameProvider
     *
     * @param string $path
     * @param string $uri
     * @param string $expected
     */
    public function testBasename(string $path, string $uri, string $expected)
    {
        $this->assertSame($expected, (string) Uri\replace_basename(Psr7\uri_for($uri), $path));
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

    /**
     * @covers \League\Uri\replace_basename
     * @covers \League\Uri\Modifiers\Basename
     */
    public function testBasenameThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\replace_basename(Psr7\uri_for('http://example.com'), 'foo/baz');
    }

    /**
     * @covers \League\Uri\replace_dirname
     * @covers \League\Uri\Modifiers\Dirname
     *
     * @dataProvider validDirnameProvider
     *
     * @param string $path
     * @param string $uri
     * @param string $expected
     */
    public function testDirname(string $path, string $uri, string $expected)
    {
        $this->assertSame($expected, (string) Uri\replace_dirname(Psr7\uri_for($uri), $path));
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
     * @covers \League\Uri\prepend_path
     * @covers \League\Uri\Modifiers\PrependSegment
     *
     * @dataProvider validPathProvider
     *
     * @param string $segment
     * @param int    $key
     * @param string $append
     * @param string $prepend
     * @param string $replace
     */
    public function testPrependProcess(string $segment, int $key, string $append, string $prepend, string $replace)
    {
        $this->assertSame($prepend, Uri\prepend_path($this->uri, $segment)->getPath());
    }

    /**
     * @covers \League\Uri\replace_segment
     * @covers \League\Uri\Modifiers\ReplaceSegment
     *
     * @dataProvider validPathProvider
     *
     * @param string $segment
     * @param int    $key
     * @param string $append
     * @param string $prepend
     * @param string $replace
     */
    public function testReplaceSegmentProcess(string $segment, int $key, string $append, string $prepend, string $replace)
    {
        $this->assertSame($replace, Uri\replace_segment($this->uri, $key, $segment)->getPath());
    }

    public function validPathProvider()
    {
        return [
            ['toto', 2, '/path/to/the/sky.php/toto', '/toto/path/to/the/sky.php', '/path/to/toto/sky.php'],
            ['le blanc', 2, '/path/to/the/sky.php/le%20blanc', '/le%20blanc/path/to/the/sky.php', '/path/to/le%20blanc/sky.php'],
        ];
    }

    /**
     * @covers \League\Uri\add_basepath
     * @covers \League\Uri\Modifiers\AddBasePath
     *
     * @dataProvider addBasePathProvider
     *
     * @param string $basepath
     * @param string $expected
     */
    public function testAddBasePath(string $basepath, string $expected)
    {
        $this->assertSame($expected, Uri\add_basepath($this->uri, $basepath)->getPath());
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

    /**
     * @covers \League\Uri\add_basepath
     * @covers \League\Uri\Modifiers\AddBasePath
     */
    public function testAddBasePathWithRelativePath()
    {
        $uri = Http::createFromString('base/path');
        $this->assertSame('/base/path', Uri\add_basepath($uri, '/base/path')->getPath());
    }

    /**
     * @covers \League\Uri\remove_basepath
     * @covers \League\Uri\Modifiers\RemoveBasePath
     *
     * @dataProvider removeBasePathProvider
     *
     * @param string $basepath
     * @param string $expected
     */
    public function testRemoveBasePath(string $basepath, string $expected)
    {
        $this->assertSame($expected, Uri\remove_basepath($this->uri, $basepath)->getPath());
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

    /**
     * @covers \League\Uri\remove_basepath
     * @covers \League\Uri\Modifiers\RemoveBasePath
     */
    public function testRemoveBasePathWithRelativePath()
    {
        $uri = Http::createFromString('base/path');
        $this->assertSame('/', Uri\remove_basepath($uri, '/base/path')->getPath());
    }

    /**
     * @covers \League\Uri\remove_segments
     * @covers \League\Uri\Modifiers\RemoveSegments
     *
     * @dataProvider validWithoutSegmentsProvider
     *
     * @param array  $keys
     * @param string $expected
     */
    public function testWithoutSegments(array $keys, string $expected)
    {
        $this->assertSame($expected, Uri\remove_segments($this->uri, $keys)->getPath());
    }

    public function validWithoutSegmentsProvider()
    {
        return [
            [[1], '/path/the/sky.php'],
        ];
    }

    /**
     * @covers \League\Uri\remove_segments
     * @covers \League\Uri\Modifiers\RemoveSegments
     *
     * @dataProvider invalidRemoveSegmentsParameters
     *
     * @param array $params
     */
    public function testRemoveSegmentsFailedConstructor(array $params)
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\remove_segments($this->uri, $params);
    }

    public function invalidRemoveSegmentsParameters()
    {
        return [
            'array contains float' => [[1, 2, '3.1']],
        ];
    }

    /**
     * @covers \League\Uri\remove_dot_segments
     * @covers \League\Uri\Modifiers\RemoveDotSegments
     */
    public function testWithoutDotSegmentsProcess()
    {
        $uri = Http::createFromString(
            'http://www.example.com/path/../to/the/./sky.php?kingkong=toto&foo=bar+baz#doc3'
        );
        $this->assertSame('/to/the/sky.php', Uri\remove_dot_segments($uri)->getPath());
    }

    /**
     * @covers \League\Uri\remove_empty_segments
     * @covers \League\Uri\Modifiers\RemoveEmptySegments
     */
    public function testWithoutEmptySegmentsProcess()
    {
        $uri = Http::createFromString(
            'http://www.example.com/path///to/the//sky.php?kingkong=toto&foo=bar+baz#doc3'
        );
        $this->assertSame('/path/to/the/sky.php', Uri\remove_empty_segments($uri)->getPath());
    }

    /**
     * @covers \League\Uri\remove_trailing_slash
     * @covers \League\Uri\Modifiers\RemoveTrailingSlash
     */
    public function testWithoutTrailingSlashProcess()
    {
        $uri = Http::createFromString('http://www.example.com/');
        $this->assertSame('', Uri\remove_trailing_slash($uri)->getPath());
    }

    /**
     * @covers \League\Uri\replace_extension
     * @covers \League\Uri\Modifiers\Extension
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @dataProvider validExtensionProvider
     *
     * @param string $extension
     * @param string $expected
     */
    public function testExtensionProcess(string $extension, string $expected)
    {
        $this->assertSame($expected, Uri\replace_extension($this->uri, $extension)->getPath());
    }

    public function validExtensionProvider()
    {
        return [
            ['csv', '/path/to/the/sky.csv'],
            ['', '/path/to/the/sky'],
        ];
    }

    /**
     * @covers \League\Uri\add_trailing_slash
     * @covers \League\Uri\Modifiers\AddTrailingSlash
     */
    public function testWithTrailingSlashProcess()
    {
        $this->assertSame('/path/to/the/sky.php/', Uri\add_trailing_slash($this->uri)->getPath());
    }

    /**
     * @covers \League\Uri\remove_leading_slash
     * @covers \League\Uri\Modifiers\RemoveLeadingSlash
     */
    public function testWithoutLeadingSlashProcess()
    {
        $uri = Http::createFromString('/foo/bar?q=b#h');

        $this->assertSame('foo/bar?q=b#h', (string) Uri\remove_leading_slash($uri));
    }

    /**
     * @covers \League\Uri\add_leading_slash
     * @covers \League\Uri\Modifiers\AddLeadingSlash
     */
    public function testWithLeadingSlashProcess()
    {
        $uri = Http::createFromString('foo/bar?q=b#h');

        $this->assertSame('/foo/bar?q=b#h', (string) Uri\add_leading_slash($uri));
    }

    /**
     * @covers \League\Uri\replace_segment
     * @covers \League\Uri\Modifiers\ReplaceSegment
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     */
    public function testReplaceSegmentConstructorFailed2()
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\replace_segment($this->uri, 2, "whyno\0t");
    }

    /**
     * @covers \League\Uri\replace_extension
     * @covers \League\Uri\Modifiers\Extension
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     */
    public function testExtensionProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\replace_extension($this->uri, 'to/to');
    }
}
