<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser\AST;

use Generator;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StatementTest extends TestCase
{
    public static function dataForInvalidSourceLines(): Generator
    {
        yield [0];
        yield [-1];
        yield [-PHP_INT_MAX];
    }

    #[DataProvider('dataForInvalidSourceLines')]
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