<?php

namespace LeagueTest\Uri\Modifiers;

use InvalidArgumentException;
use League\Uri\Components\Host;
use League\Uri\Components\Query;
use League\Uri\Components\Scheme;
use League\Uri\Modifiers\Formatter;
use League\Uri\Schemes\Data;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group formatter
 */
class FormatterTest extends TestCase
{
    /**
     * @var Http
     */
    private $uri;

    /**
     * @var Formatter
     */
    private $formatter;

    protected function setUp()
    {
        $this->uri = Http::createFromString(
            'http://login:pass@gwóźdź.pl:443/test/query.php?kingkong=toto&foo=bar+baz#doc3'
        );
        $this->formatter = new Formatter();
    }

    public function testFormatHostAscii()
    {
        $this->formatter->setEncoding(Formatter::RFC3986_ENCODING);
        $this->assertSame('xn--gwd-hna98db.pl', $this->formatter->__invoke(new Host('gwóźdź.pl')));
    }

    public function testInvalidHostEncoding()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->formatter->setEncoding('toto');
    }

    public function testFormatWithSimpleString()
    {
        $uri = 'https://login:pass@gwóźdź.pl:443/test/query.php?kingkong=toto&foo=bar+baz#doc3';
        $expected = 'https://login:pass@xn--gwd-hna98db.pl/test/query.php?kingkong=toto&amp;foo=bar+baz#doc3';
        $uri = Http::createFromString($uri);

        $this->formatter->setQuerySeparator('&amp;');
        $this->formatter->setEncoding(Formatter::RFC3986_ENCODING);
        $this->assertSame($expected, $this->formatter->__invoke($uri));
    }

    public function testFormatWithZeroes()
    {
        $expected = 'https://example.com/image.jpeg?0#0';
        $uri = Http::createFromString('https://example.com/image.jpeg?0#0');
        $this->assertSame($expected, $this->formatter->__invoke($uri));
    }

    public function testFormatComponent()
    {
        $scheme = new Scheme('ftp');
        $this->assertSame($scheme->__toString(), $this->formatter->__invoke($scheme));
    }

    public function testFormatHostUnicode()
    {
        $this->formatter->setEncoding(Formatter::RFC3987_ENCODING);
        $this->assertSame('gwóźdź.pl', $this->formatter->__invoke(new Host('gwóźdź.pl')));
    }

    public function testFormatQueryRFC3986()
    {
        $this->assertSame('kingkong=toto&foo=bar+baz', $this->formatter->__invoke(new Query('kingkong=toto&foo=bar+baz')));
    }

    public function testFormatQueryWithSeparator()
    {
        $this->formatter->setQuerySeparator('&amp;');
        $this->assertSame(
            'kingkong=toto&amp;foo=bar+baz',
            $this->formatter->__invoke(new Query('kingkong=toto&foo=bar+baz'))
        );
    }

    public function testFormat()
    {
        $this->formatter->setQuerySeparator('&amp;');
        $this->formatter->setEncoding(Formatter::RFC3986_ENCODING);
        $expected = 'http://login:pass@xn--gwd-hna98db.pl:443/test/query.php?kingkong=toto&amp;foo=bar+baz#doc3';
        $this->assertSame($expected, $this->formatter->__invoke($this->uri));
    }

    public function testFormatOpaqueUri()
    {
        $uri = Data::createFromString('data:,');
        $this->formatter->setQuerySeparator('&amp;');
        $this->formatter->setEncoding(Formatter::RFC3986_ENCODING);
        $this->assertSame($uri->__toString(), $this->formatter->__invoke($uri));
    }

    public function testFormatWithoutAuthority()
    {
        $expected = '/test/query.php?kingkong=toto&amp;foo=bar+baz#doc3';
        $uri = Http::createFromString('/test/query.php?kingkong=toto&foo=bar+baz#doc3');
        $this->formatter->setQuerySeparator('&amp;');
        $this->formatter->setEncoding(Formatter::RFC3986_ENCODING);
        $this->assertSame($expected, $this->formatter->__invoke($uri));
    }

    public function testFormatterFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->formatter->__invoke('http://www.example.com');
    }

    public function testFormatterPreservedQuery()
    {
        $expected = 'http://example.com';
        $uri = Http::createFromString($expected);
        $this->formatter->preserveQuery(true);
        $this->assertSame($expected, (string) $uri);
        $this->assertSame('http://example.com?', $this->formatter->__invoke($uri));
    }

    public function testFormatterPreservedFragment()
    {
        $expected = 'http://example.com';
        $uri = Http::createFromString($expected);
        $this->formatter->preserveFragment(true);
        $this->assertSame($expected, (string) $uri);
        $this->assertSame('http://example.com#', $this->formatter->__invoke($uri));
    }
}
