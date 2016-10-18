<?php

namespace LeagueTest\Uri\Manipulations;

use InvalidArgumentException;
use League\Uri\Components\Query;
use League\Uri\Manipulations\FilterQuery;
use League\Uri\Manipulations\KsortQuery;
use League\Uri\Manipulations\MergeQuery;
use League\Uri\Manipulations\RemoveQueryKeys;
use League\Uri\Schemes\Http as HttpUri;
use PHPUnit_Framework_TestCase;

/**
 * @group query
 * @group modifier
 * @group query-modifier
 */
class QueryManipulatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpUri
     */
    private $uri;

    protected function setUp()
    {
        $this->uri = HttpUri::createFromString(
            'http://www.example.com/path/to/the/sky.php?kingkong=toto&foo=bar%20baz#doc3'
        );
    }

    public function testFilterQueryParameters()
    {
        $modifier = new FilterQuery(function ($value) {
            return $value == 'kingkong';
        }, ARRAY_FILTER_USE_KEY);

        $this->assertSame('kingkong=toto', $modifier->__invoke($this->uri)->getQuery());
    }

    public function testFilterQueryValues()
    {
        $filter = function ($value) {
            return $value == 'toto';
        };

        $modifier = new FilterQuery($filter, 0);

        $this->assertSame('kingkong=toto', $modifier->__invoke($this->uri)->getQuery());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFilterFlagFailed()
    {
        new FilterQuery(function ($value) {
            return $value == 'toto';
        }, 'toto');
    }

    /**
     * @dataProvider validQueryProvider
     *
     * @param string $query
     * @param string $expected
     */
    public function testMergeQuery($query, $expected)
    {
        $modifier = new MergeQuery($query);

        $this->assertSame($expected, $modifier->__invoke($this->uri)->getQuery());
    }

    public function validQueryProvider()
    {
        return [
            ['toto', 'kingkong=toto&foo=bar%20baz&toto'],
            ['toto=&toto=1', 'kingkong=toto&foo=bar%20baz&toto=&toto=1'],
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

    /**
     * @expectedException InvalidArgumentException
     */
    public function testKsortQueryFailed()
    {
        new KsortQuery(['data']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMergeQueryContructorFailed()
    {
        new MergeQuery(new Query('toto=king'));
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
            [[1], 'kingkong=toto&foo=bar%20baz'],
        ];
    }
}
