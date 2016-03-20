<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser;


use Helmich\TypoScriptParser\Parser\ParseError;

class ParseErrorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ParseError */
    private $exc;

    public function setUp()
    {
        $this->exc = new ParseError('foobar', 1234, 4321);
    }

    public function testCanSetSourceLine()
    {
        $this->assertEquals(4321, $this->exc->getSourceLine());
    }

    public function testCanSetMessage()
    {
        $this->assertEquals('foobar', $this->exc->getMessage());
    }
}