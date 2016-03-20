<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser\AST;


use Helmich\TypoScriptParser\Parser\AST\Statement;

class StatementTest extends \PHPUnit_Framework_TestCase
{
    public function dataForInvalidSourceLines()
    {
        yield [0];
        yield [0.1];
        yield [-0.1];
        yield [-1];
        yield [-PHP_INT_MAX];
    }

    /**
     * @dataProvider dataForInvalidSourceLines
     * @expectedException \InvalidArgumentException
     * @param $invalidSourceLine
     */
    public function testInvalidSourceLineThrowsException($invalidSourceLine)
    {
        $statement = $this
            ->getMockBuilder(Statement::class)
            ->setConstructorArgs([$invalidSourceLine])
            ->enableOriginalConstructor()
            ->getMockForAbstractClass();
    }
}