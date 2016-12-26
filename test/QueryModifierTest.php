<?php

namespace LeagueTest\Uri\Modifiers;

use InvalidArgumentException;
use League\Uri\Components\Query;
use League\Uri\Modifiers\AppendQuery;
use League\Uri\Modifiers\KsortQuery;
use League\Uri\Modifiers\MergeQuery;
use League\Uri\Modifiers\RemoveQueryKeys;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group query
 * @group modifier
 * @group query-modifier
 */
class QueryManipulatorTest extends TestCase
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
     * @dataProvider validMergeQueryProvider
     *
     * @param string $query
     * @param string $expected
     */
    public function testMergeQuery($query, $expected)
    {
        $modifier = new MergeQuery($query);

        $this->assertSame($expected, $modifier->__invoke($this->uri)->getQuery());
    }

    public function validMergeQueryProvider()
    {
        return [
            ['toto', 'kingkong=toto&foo=bar%20baz&toto'],
            ['kingkong=ape', 'kingkong=ape&foo=bar%20baz'],
        ];
    }

    /**
     * @dataProvider validAppendQueryProvider
     */
    public function testAppendQuery($query, $expected)
    {
        $modifier = new AppendQuery($query);

        $this->assertSame($expected, $modifier->__invoke($this->uri)->getQuery());
    }

    public function validAppendQueryProvider()
    {
        return [
            ['toto', 'kingkong=toto&foo=bar%20baz&toto'],
            ['kingkong=ape', 'kingkong=toto&kingkong=ape&foo=bar%20baz'],
        ];
    }

    /**
     * @dataProvider validQueryKsortProvider
     *
     * @param int|callable $input
     * @param string       $expected
     */
    public function testKsortQuery($input, $expected)
    {
        $modifier = new KsortQuery($input);

        $this->assertSame($expected, $modifier->__invoke($this->uri)->getQuery());
    }

    //?kingkong=toto&foo=bar+baz

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
        new KsortQuery(['data']);
    }

    /**
     * @dataProvider validWithoutQueryValuesProvider
     *
     * @param array  $input
     * @param string $expected
     */
    public function testWithoutQueryValuesProcess($input, $expected)
    {
        $modifier = new RemoveQueryKeys($input);

        $this->assertSame($expected, $modifier->__invoke($this->uri)->getQuery());
    }

    public function validWithoutQueryValuesProvider()
    {
        return [
            [['1'], 'kingkong=toto&foo=bar%20baz'],
        ];
    }
}
