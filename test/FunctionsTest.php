<?php

namespace LeagueTest\Uri\Modifiers;

use InvalidArgumentException;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;
use function League\Uri\Modifiers\uri_reference;

/**
 * @group functions
 */
class FunctionsTest extends TestCase
{
    /**
     * @dataProvider uriProvider
     */
    public function testStat($uri, $base_uri, $infos)
    {
        $this->assertSame($infos, uri_reference($uri, $base_uri));
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
     */
    public function testStatThrowsInvalidArgumentException($uri, $base_uri)
    {
        $this->expectException(InvalidArgumentException::class);
        uri_reference($uri, $base_uri);
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
}
