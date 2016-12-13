<?php

namespace LeagueTest\Uri\Modifiers;

use GuzzleHttp\Psr7\Uri as GuzzleUri;
use InvalidArgumentException;
use League\Uri\Components\Host;
use League\Uri\Modifiers\AddRootLabel;
use League\Uri\Modifiers\AppendLabel;
use League\Uri\Modifiers\FilterLabels;
use League\Uri\Modifiers\HostToAscii;
use League\Uri\Modifiers\HostToUnicode;
use League\Uri\Modifiers\PrependLabel;
use League\Uri\Modifiers\RegisterableDomain;
use League\Uri\Modifiers\RemoveLabels;
use League\Uri\Modifiers\RemoveRootLabel;
use League\Uri\Modifiers\RemoveZoneIdentifier;
use League\Uri\Modifiers\ReplaceLabel;
use League\Uri\Modifiers\Subdomain;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;

/**
 * @group host
 * @group modifier
 */
class HostManipulatorTest extends TestCase
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
     * @param string $label
     * @param int    $key
     * @param string $prepend
     * @param string $append
     * @param string $replace
     */
    public function testPrependLabelProcess($label, $key, $prepend, $append, $replace)
    {
        $modifier = new PrependLabel($label);
        $this->assertSame($prepend, $modifier($this->uri)->getHost());
    }

    /**
     * @dataProvider validHostProvider
     *
     * @param string $label
     * @param int    $key
     * @param string $prepend
     * @param string $append
     * @param string $replace
     */
    public function testAppendLabelProcess($label, $key, $prepend, $append, $replace)
    {
        $modifier = new AppendLabel($label);
        $this->assertSame($append, $modifier($this->uri)->getHost());
    }

    /**
     * @dataProvider validHostProvider
     *
     * @param string $label
     * @param int    $key
     * @param string $prepend
     * @param string $append
     * @param string $replace
     */
    public function testReplaceLabelProcess($label, $key, $prepend, $append, $replace)
    {
        $modifier = new ReplaceLabel($key, $label);
        $this->assertSame($replace, $modifier($this->uri)->getHost());
    }

    public function validHostProvider()
    {
        return [
            ['toto', 2, 'toto.www.example.com', 'www.example.com.toto', 'toto.example.com'],
            ['123', 1, '123.www.example.com', 'www.example.com.123', 'www.123.com'],
        ];
    }

    public function testHostToAsciiProcess()
    {
        $uri = Http::createFromString('http://مثال.إختبار/where/to/go');
        $modifier = new HostToAscii();
        $this->assertSame(
            'http://xn--mgbh0fb.xn--kgbechtv/where/to/go',
            (string) $modifier($uri)
        );
    }

    public function testHostToUnicodeProcess()
    {
        $uri = new GuzzleUri('http://xn--mgbh0fb.xn--kgbechtv/where/to/go');
        $modifier = new HostToUnicode();
        $this->assertSame(
            'http://مثال.إختبار/where/to/go',
            (string) $modifier($uri)
        );
    }

    public function testWithoutZoneIdentifierProcess()
    {
        $modifier = new RemoveZoneIdentifier();
        $uri = Http::createFromString('http://[fe80::1234%25eth0-1]/path/to/the/sky.php');
        $this->assertSame(
            'http://[fe80::1234]/path/to/the/sky.php',
            (string) $modifier($uri)
        );
    }

    /**
     * @dataProvider validWithoutLabelsProvider
     *
     * @param array  $keys
     * @param string $expected
     */
    public function testWithoutLabelsProcess($keys, $expected)
    {
        $modifier = new RemoveLabels($keys);
        $this->assertSame($expected, $modifier($this->uri)->getHost());
    }

    public function validWithoutLabelsProvider()
    {
        return [
            [[1], 'www.com'],
        ];
    }

    public function testFilterLabels()
    {
        $modifier = new FilterLabels(function ($value) {
            return strpos($value, 'w') === false;
        });

        $this->assertSame('example.com', (string) $modifier($this->uri)->getHost());
    }

    public function testRemoveLabels()
    {
        $modifier = new RemoveLabels([2]);
        $this->assertSame('example.com', (string) $modifier($this->uri)->getHost());
    }

    public function testSubdomain()
    {
        $modifier = new Subdomain('shop');
        $this->assertSame('shop.example.com', (string) $modifier($this->uri)->getHost());
    }

    public function testRegisterableDomain()
    {
        $modifier = new RegisterableDomain('ulb.ac.be');
        $this->assertSame('www.ulb.ac.be', (string) $modifier($this->uri)->getHost());
    }

    public function testAddRootLabel()
    {
        $modifier = new AddRootLabel();
        $this->assertSame('www.example.com.', (string) $modifier($this->uri)->getHost());
    }

    public function testRemoveRootLabel()
    {
        $modifier = new RemoveRootLabel();
        $this->assertSame('www.example.com', (string) $modifier($this->uri)->getHost());
    }

    public function testHostToUnicodeProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new HostToUnicode())->__invoke('http://www.example.com');
    }

    public function testRegisterableDomainFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new RegisterableDomain('example.com.');
    }

    public function testRegisterableDomainFailedWithIpaddress()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RegisterableDomain('example.com'))->__invoke($this->uri->withHost('127.0.0.1'));
    }

    public function testSubdomainFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new Subdomain('example.com.');
    }

    public function testSubdomainFailedWithIpaddress()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Subdomain('example.com'))->__invoke($this->uri->withHost('127.0.0.1'));
    }

    public function testHostToAsciiProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new HostToAscii())->__invoke('http://www.example.com');
    }

    public function testWithoutZoneIdentifierProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new RemoveZoneIdentifier())->__invoke('http://www.example.com');
    }

    public function testAppendLabelProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AppendLabel(''))->__invoke('http://www.example.com');
    }

    public function testAppendLabelConstructorFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new AppendLabel(new Host('example.com'));
    }

    public function testPrependLabelConstructorFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new PrependLabel(new Host('example.com'));
    }

    public function testPrependLabelProcessFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        (new PrependLabel(''))->__invoke('http://www.example.com');
    }

    public function testReplaceLabelConstructorFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceLabel(-3, new Host('toto'));
    }

    public function testReplaceLabelConstructorFailedWithInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceLabel('toto', new Host('toto'));
    }
}
