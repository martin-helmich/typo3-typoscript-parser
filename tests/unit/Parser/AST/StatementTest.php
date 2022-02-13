<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser\AST;

use Helmich\TypoScriptParser\Parser\AST\Statement;
use PHPUnit\Framework\TestCase;

class StatementTest extends TestCase
{
    public function dataForInvalidSourceLines()
    {
        yield [0];
        yield [-1];
        yield [-PHP_INT_MAX];
    }

    /**
     * @dataProvider dataForInvalidSourceLines
     * @param $invalidSourceLine
     */
    public function testInvalidSourceLineThrowsException($invalidSourceLine)
    {
        $this->expectException(\InvalidArgumentException::class);
        $statement = $this
            ->getMockBuilder(Statement::class)
            ->setConstructorArgs([$invalidSourceLine])
            ->enableOriginalConstructor()
            ->getMockForAbstractClass();
    }
}