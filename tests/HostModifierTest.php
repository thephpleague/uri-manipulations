<?php

namespace LeagueTest\Uri;

use GuzzleHttp\Psr7\Uri as GuzzleUri;
use InvalidArgumentException;
use League\Uri;
use League\Uri\Components\Host;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group host
 * @group modifier
 */
class HostModifierTest extends TestCase
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
     * @dataProvider validHostProvider
     *
     * @covers \League\Uri\prepend_host
     * @covers \League\Uri\Modifiers\PrependLabel
     * @covers \League\Uri\Modifiers\HostMiddlewareTrait<extended>
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @param string $label
     * @param int    $key
     * @param string $prepend
     * @param string $append
     * @param string $replace
     */
    public function testPrependLabelProcess(string $label, int $key, string $prepend, string $append, string $replace)
    {
        $this->assertSame($prepend, Uri\prepend_host($this->uri, $label)->getHost());
    }

    /**
     * @dataProvider validHostProvider
     *
     * @covers \League\Uri\append_host
     * @covers \League\Uri\Modifiers\AppendLabel
     *
     * @param string $label
     * @param int    $key
     * @param string $prepend
     * @param string $append
     * @param string $replace
     */
    public function testAppendLabelProcess(string $label, int $key, string $prepend, string $append, string $replace)
    {
        $this->assertSame($append, Uri\append_host($this->uri, $label)->getHost());
    }

    /**
     * @dataProvider validHostProvider
     *
     * @covers \League\Uri\replace_label
     * @covers \League\Uri\Modifiers\ReplaceLabel
     *
     * @param string $label
     * @param int    $key
     * @param string $prepend
     * @param string $append
     * @param string $replace
     */
    public function testReplaceLabelProcess(string $label, int $key, string $prepend, string $append, string $replace)
    {
        $this->assertSame($replace, Uri\replace_label($this->uri, $key, $label)->getHost());
    }

    public function validHostProvider()
    {
        return [
            ['toto', 2, 'toto.www.example.com', 'www.example.com.toto', 'toto.example.com'],
            ['123', 1, '123.www.example.com', 'www.example.com.123', 'www.123.com'],
        ];
    }

    /**
     * @covers \League\Uri\host_to_ascii
     * @covers \League\Uri\Modifiers\HostToAscii
     */
    public function testHostToAsciiProcess()
    {
        $uri = Http::createFromString('http://مثال.إختبار/where/to/go');
        $this->assertSame(
            'http://xn--mgbh0fb.xn--kgbechtv/where/to/go',
            (string) Uri\host_to_ascii($uri)
        );
    }

    /**
     * @covers \League\Uri\host_to_unicode
     * @covers \League\Uri\Modifiers\HostToUnicode
     */
    public function testHostToUnicodeProcess()
    {
        $uri = new GuzzleUri('http://xn--mgbh0fb.xn--kgbechtv/where/to/go');
        $expected = 'http://مثال.إختبار/where/to/go';
        $this->assertSame($expected, (string) Uri\host_to_unicode($uri));
    }

    /**
     * @covers \League\Uri\remove_zone_id
     * @covers \League\Uri\Modifiers\RemoveZoneIdentifier
     */
    public function testWithoutZoneIdentifierProcess()
    {
        $uri = Http::createFromString('http://[fe80::1234%25eth0-1]/path/to/the/sky.php');
        $this->assertSame(
            'http://[fe80::1234]/path/to/the/sky.php',
            (string) Uri\remove_zone_id($uri)
        );
    }

    /**
     * @covers \League\Uri\remove_labels
     * @covers \League\Uri\Modifiers\RemoveLabels
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @dataProvider validWithoutLabelsProvider
     *
     * @param array  $keys
     * @param string $expected
     */
    public function testWithoutLabelsProcess(array $keys, string $expected)
    {
        $this->assertSame($expected, Uri\remove_labels($this->uri, $keys)->getHost());
    }

    public function validWithoutLabelsProvider()
    {
        return [
            [[1], 'www.com'],
        ];
    }

    /**
     * @covers \League\Uri\remove_labels
     * @covers \League\Uri\Modifiers\RemoveLabels
     */
    public function testRemoveLabels()
    {
        $this->assertSame('example.com', Uri\remove_labels($this->uri, [2])->getHost());
    }

    /**
     * @covers \League\Uri\remove_labels
     * @covers \League\Uri\Modifiers\RemoveLabels
     * @covers \League\Uri\Modifiers\UriMiddlewareInterface
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @dataProvider invalidRemoveLabelsParameters
     *
     * @param array $params
     */
    public function testRemoveLabelsFailedConstructor(array $params)
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\remove_labels($this->uri, $params);
    }

    public function invalidRemoveLabelsParameters()
    {
        return [
            'array contains float' => [[1, 2, '3.1']],
        ];
    }

    /**
     * @covers \League\Uri\replace_subdomain
     * @covers \League\Uri\Modifiers\Subdomain
     */
    public function testSubdomain()
    {
        $this->assertSame('shop.example.com', Uri\replace_subdomain($this->uri, 'shop')->getHost());
    }

    /**
     * @covers \League\Uri\replace_registrabledomain
     * @covers \League\Uri\Modifiers\RegisterableDomain
     */
    public function testRegisterableDomain()
    {
        $this->assertSame('www.ulb.ac.be', Uri\replace_registrabledomain($this->uri, 'ulb.ac.be')->getHost());
    }

    /**
     * @covers \League\Uri\replace_publicsuffix
     * @covers \League\Uri\Modifiers\PublicSuffix
     */
    public function testPublicSuffix()
    {
        $this->assertSame('www.example.fr', Uri\replace_publicsuffix($this->uri, 'fr')->getHost());
    }

    /**
     * @covers \League\Uri\add_root_label
     * @covers \League\Uri\Modifiers\AddRootLabel
     */
    public function testAddRootLabel()
    {
        $this->assertSame('www.example.com.', Uri\add_root_label($this->uri)->getHost());
    }

    /**
     * @covers \League\Uri\remove_root_label
     * @covers \League\Uri\Modifiers\RemoveRootLabel
     */
    public function testRemoveRootLabel()
    {
        $this->assertSame('www.example.com', Uri\remove_root_label($this->uri)->getHost());
    }

    /**
     * @covers \League\Uri\replace_publicsuffix
     * @covers \League\Uri\Modifiers\PublicSuffix
     */
    public function testPublicSuffixFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\replace_publicsuffix($this->uri, 'example.com.');
    }

    /**
     * @covers \League\Uri\replace_registrabledomain
     * @covers \League\Uri\Modifiers\RegisterableDomain
     */
    public function testRegisterableDomainFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\replace_registrabledomain($this->uri, 'example.com.');
    }

    /**
     * @covers \League\Uri\replace_subdomain
     * @covers \League\Uri\Modifiers\Subdomain
     */
    public function testSubdomainFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\replace_subdomain($this->uri, 'example.com.');
    }
}
