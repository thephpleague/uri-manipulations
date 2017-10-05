<?php

namespace LeagueTest\Uri;

use InvalidArgumentException;
use League\Uri;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group functions
 */
class FunctionsTest extends TestCase
{
    /**
     * @dataProvider uriProvider
     *
     * @covers \League\Uri\Modifiers\uri_reference
     * @covers \League\Uri\is_absolute
     * @covers \League\Uri\is_absolute_path
     * @covers \League\Uri\is_network_path
     * @covers \League\Uri\is_relative_path
     * @covers \League\Uri\is_same_document
     *
     * @param mixed  $uri
     * @param mixed  $base_uri
     * @param bool[] $infos
     */
    public function testStat($uri, $base_uri, array $infos)
    {
        $this->assertSame($infos, Uri\Modifiers\uri_reference($uri, $base_uri));
        if (null !== $base_uri) {
            $this->assertSame($infos['same_document'], Uri\is_same_document($uri, $base_uri));
        }
        $this->assertSame($infos['relative_path'], Uri\is_relative_path($uri));
        $this->assertSame($infos['absolute_path'], Uri\is_absolute_path($uri));
        $this->assertSame($infos['absolute_uri'], Uri\is_absolute($uri));
        $this->assertSame($infos['network_path'], Uri\is_network_path($uri));
    }

    public function uriProvider()
    {
        return [
            'absolute uri' => [
                'uri' => Http::createFromString('http://a/p?q#f'),
                'base_uri' => null,
                'infos' => [
                    'absolute_uri' => true,
                    'network_path' => false,
                    'absolute_path' => false,
                    'relative_path' => false,
                    'same_document' => false,
                ],
            ],
            'network relative uri' => [
                'uri' => Http::createFromString('//스타벅스코리아.com/p?q#f'),
                'base_uri' => Http::createFromString('//xn--oy2b35ckwhba574atvuzkc.com/p?q#z'),
                'infos' => [
                    'absolute_uri' => false,
                    'network_path' => true,
                    'absolute_path' => false,
                    'relative_path' => false,
                    'same_document' => true,
                ],
            ],
            'path absolute uri' => [
                'uri' => Http::createFromString('/p?q#f'),
                'base_uri' => Http::createFromString('/p?a#f'),
                'infos' => [
                    'absolute_uri' => false,
                    'network_path' => false,
                    'absolute_path' => true,
                    'relative_path' => false,
                    'same_document' => false,
                ],
            ],
            'path relative uri with non empty path' => [
                'uri' => Http::createFromString('p?q#f'),
                'base_uri' => null,
                'infos' => [
                    'absolute_uri' => false,
                    'network_path' => false,
                    'absolute_path' => false,
                    'relative_path' => true,
                    'same_document' => false,
                ],
            ],
            'path relative uri with empty' => [
                'uri' => Http::createFromString('?q#f'),
                'base_uri' => null,
                'infos' => [
                    'absolute_uri' => false,
                    'network_path' => false,
                    'absolute_path' => false,
                    'relative_path' => true,
                    'same_document' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider failedUriProvider
     *
     * @covers \League\Uri\Modifiers\uri_reference
     * @covers \League\Uri\is_same_document
     *
     * @param mixed $uri
     * @param mixed $base_uri
     */
    public function testStatThrowsInvalidArgumentException($uri, $base_uri)
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\is_same_document($uri, $base_uri);
    }

    public function failedUriProvider()
    {
        return [
            'invalid uri' => [
                'uri' => Http::createFromString('http://a/p?q#f'),
                'base_uri' => 'http://example.com',
            ],
            'invalid base uri' => [
                'uri' => 'http://example.com',
                'base_uri' => Http::createFromString('//a/p?q#f'),
            ],
        ];
    }

    /**
     * @dataProvider dataUriStringProvider
     *
     * @covers \League\Uri\uri_to_rfc3986
     * @covers \League\Uri\uri_to_rfc3987
     *
     * @param string $str
     * @param string $rfc3986
     * @param string $rfc3987
     */
    public function testUriConversion($str, $rfc3986, $rfc3987)
    {
        $uri = Uri\Schemes\Http::createFromString($str);
        $this->assertSame($rfc3986, Uri\uri_to_rfc3986($uri));
        $this->assertSame($rfc3987, Uri\uri_to_rfc3987($uri));
    }

    public function dataUriStringProvider()
    {
        return [
            'mixed content' => [
                'http://xn--bb-bjab.be/toto/тестовый_путь/',
                'http://xn--bb-bjab.be/toto/%D1%82%D0%B5%D1%81%D1%82%D0%BE%D0%B2%D1%8B%D0%B9_%D0%BF%D1%83%D1%82%D1%8C/',
                'http://bébé.be/toto/тестовый_путь/',
            ],
            'host punycoded' => [
                'https://ουτοπία.δπθ.gr',
                'https://xn--kxae4bafwg.xn--pxaix.gr',
                'https://ουτοπία.δπθ.gr',
            ],
            'preserve both delimiters' => [
                'https://example.com/?#',
                'https://example.com/?#',
                'https://example.com/?#',
            ],
            'preserve fragment delimiters' => [
                'https://example.com/#',
                'https://example.com/#',
                'https://example.com/#',
            ],
            'preserve query delimiters' => [
                'https://example.com/?',
                'https://example.com/?',
                'https://example.com/?',
            ],
        ];
    }
}
