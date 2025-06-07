<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tests\Functional\Parser;

use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Parser\StatementDumper;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StatementDumperTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function dataForPrinterTest(): array
    {
        $files = glob(__DIR__ . '/Fixtures/*/*.typoscript');
        assert($files !== false);

        $testCases = [];
        foreach ($files as $input) {
            self::assertFileExists($input . '.dump.txt');

            $inputContent = file_get_contents($input);
            assert($inputContent !== false);
            $outputContent = file_get_contents($input . '.dump.txt');
            assert($outputContent !== false);

            $testGroup = basename(dirname($input));
            $testCases[$testGroup . ' / ' . str_replace('.typoscript', '', basename($input))] = [$inputContent, $outputContent];
        }

        return $testCases;
    }

    #[DataProvider('dataForPrinterTest')]
    public function testParsedCodeIsCorrectlyPrinted(string $input, string $expectedOutput): void
    {
        $statementDumper = new StatementDumper();
        $parser = new Parser(new Tokenizer());
        $statements = $parser->parseString($input);

        $this->assertEquals($expectedOutput, $statementDumper->dump($statements));
    }
}
