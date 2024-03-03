<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser;

use Helmich\TypoScriptParser\Parser\ParseError;
use PHPUnit\Framework\TestCase;

class ParseErrorTest extends TestCase
{
    private ParseError $exc;

    public function setUp(): void
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