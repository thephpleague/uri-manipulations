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
     * @covers \League\Uri\sort_query_keys
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
        $this->assertSame($expected, Uri\sort_query_keys($this->uri, $sort)->getQuery());
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
        Uri\sort_query_keys($this->uri, ['data']);
    }

    /**
     * @covers \League\Uri\remove_query_values
     * @covers \League\Uri\Modifiers\RemoveQueryKeys
     *
     * @dataProvider validWithoutQueryValuesProvider
     *
     * @param array  $input
     * @param string $expected
     */
    public function testWithoutQueryValuesProcess(array $input, $expected)
    {
        $this->assertSame($expected, Uri\remove_query_values($this->uri, $input)->getQuery());
    }

    public function validWithoutQueryValuesProvider()
    {
        return [
            [['1'], 'kingkong=toto&foo=bar%20baz'],
        ];
    }

    /**
     * @dataProvider parsedQueryProvider
     * @param string $query
     * @param array  $expectedData
     */
    public function testParsedQuery($query, $expectedData)
    {
        $this->assertSame($expectedData, Uri\parse_query($query));
    }

    public function parsedQueryProvider()
    {
        return [
            [
                'query' => '&&',
                'expected' => [],
            ],
            [
                'query' => 'arr[1=sid&arr[4][2=fred',
                'expected' => [
                    'arr[1' => 'sid',
                    'arr' => ['4' => 'fred'],
                ],
            ],
            [
                'query' => 'arr1]=sid&arr[4]2]=fred',
                'expected' => [
                    'arr1]' => 'sid',
                    'arr' => ['4' => 'fred'],
                ],
            ],
            [
                'query' => 'arr[one=sid&arr[4][two=fred',
                'expected' => [
                    'arr[one' => 'sid',
                    'arr' => ['4' => 'fred'],
                ],
            ],
            [
                'query' => 'first=%41&second=%a&third=%b',
                'expected' => [
                    'first' => 'A',
                    'second' => '%a',
                    'third' => '%b',
                ],
            ],
            [
                'query' => 'arr.test[1]=sid&arr test[4][two]=fred',
                'expected' => [
                    'arr.test' => ['1' => 'sid'],
                    'arr test' => ['4' => ['two' => 'fred']],
                ],
            ],
            [
                'query' => 'foo&bar=&baz=bar&fo.o',
                'expected' => [
                    'foo' => '',
                    'bar' => '',
                    'baz' => 'bar',
                    'fo.o' => '',
                ],
            ],
            [
                'query' => 'foo[]=bar&foo[]=baz',
                'expected' => [
                    'foo' => ['bar', 'baz'],
                ],
            ],
        ];
    }
}
