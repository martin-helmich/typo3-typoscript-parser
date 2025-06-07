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
    public function testInvalidSourceLineThrowsException(int $invalidSourceLine): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $_ = new class($invalidSourceLine) extends Statement {
            public function getSubNodeNames(): array
            {
                return [];
            }
        };
    }
}
