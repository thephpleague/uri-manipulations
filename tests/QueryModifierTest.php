<?php

namespace LeagueTest\Uri;

use InvalidArgumentException;
use League\Uri;
use League\Uri\Components\Query;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group query
 * @group modifier
 * @group query-modifier
 */
class QueryModifierTest extends TestCase
{
    /**
     * @var Http
     */
    private $uri;

    protected function setUp()
    {
        $this->uri = Http::createFromString(
            'http://www.example.com/path/to/the/sky.php?kingkong=toto&foo=bar%20baz#doc3'
        );
    }

    /**
     * @covers \League\Uri\merge_query
     * @covers \League\Uri\Modifiers\MergeQuery
     *
     * @dataProvider validMergeQueryProvider
     *
     * @param string $query
     * @param string $expected
     */
    public function testMergeQuery(string $query, string $expected)
    {
        $this->assertSame($expected, Uri\merge_query($this->uri, $query)->getQuery());
    }

    public function validMergeQueryProvider()
    {
        return [
            ['toto', 'kingkong=toto&foo=bar%20baz&toto'],
            ['kingkong=ape', 'kingkong=ape&foo=bar%20baz'],
        ];
    }

    /**
     * @covers \League\Uri\append_query
     * @covers \League\Uri\Modifiers\AppendQuery
     * @covers \League\Uri\Modifiers\QueryMiddlewareTrait<extended>
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @dataProvider validAppendQueryProvider
     *
     * @param string $query
     * @param string $expected
     */
    public function testAppendQuery(string $query, string $expected)
    {
        $this->assertSame($expected, Uri\append_query($this->uri, $query)->getQuery());
    }

    public function validAppendQueryProvider()
    {
        return [
            ['toto', 'kingkong=toto&foo=bar%20baz&toto'],
            ['kingkong=ape', 'kingkong=toto&kingkong=ape&foo=bar%20baz'],
        ];
    }

    /**
     * @covers \League\Uri\sort_query
     * @covers \League\Uri\Modifiers\KsortQuery
     * @covers \League\Uri\Modifiers\UriMiddlewareTrait<extended>
     *
     * @dataProvider validQueryKsortProvider
     *
     * @param int|callable $sort
     * @param string       $expected
     */
    public function testKsortQuery($sort, $expected)
    {
        $this->assertSame($expected, Uri\sort_query($this->uri, $sort)->getQuery());
    }

    public function validQueryKsortProvider()
    {
        return [
            [SORT_REGULAR, 'foo=bar%20baz&kingkong=toto'],
            [function ($value1, $value2) {
                return strcasecmp($value1, $value2);
            }, 'foo=bar%20baz&kingkong=toto'],
        ];
    }

    public function testKsortQueryFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        Uri\sort_query($this->uri, ['data']);
    }

    /**
     * @covers \League\Uri\remove_pairs
     * @covers \League\Uri\Modifiers\RemoveQueryKeys
     *
     * @dataProvider validWithoutQueryValuesProvider
     *
     * @param array  $input
     * @param string $expected
     */
    public function testWithoutQueryValuesProcess(array $input, $expected)
    {
        $this->assertSame($expected, Uri\remove_pairs($this->uri, $input)->getQuery());
    }

    public function validWithoutQueryValuesProvider()
    {
        return [
            [['1'], 'kingkong=toto&foo=bar%20baz'],
            [['kingkong'], 'foo=bar%20baz'],
        ];
    }

    /**
     * @covers \League\Uri\remove_params
     * @covers \League\Uri\Modifiers\RemoveQueryParams
     *
     * @dataProvider removeParamsProvider
     * @param string $uri
     * @param array  $input
     * @param string $expected
     */
    public function testWithoutQueryParams(string $uri, array $input, string $expected)
    {
        $this->assertSame($expected, Uri\remove_params(Uri\create($uri), $input)->getQuery());
    }

    public function removeParamsProvider()
    {
        return [
            [
                'uri' => 'http://example.com',
                'input' => ['foo'],
                'expected' => '',
            ],
            [
                'uri' => 'http://example.com?foo=bar&bar=baz',
                'input' => ['foo'],
                'expected' => 'bar=baz',
            ],
            [
                'uri' => 'http://example.com?fo.o=bar&fo_o=baz',
                'input' => ['fo_o'],
                'expected' => 'fo.o=bar',
            ],
        ];
    }
}
