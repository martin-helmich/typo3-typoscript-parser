<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tests\Functional\Parser;

use Generator;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Parser\Printer\ASTPrinterInterface;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinter;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConfiguration;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

class ParserPrinterTest extends TestCase
{
    private ASTPrinterInterface $printer;
    private Parser $parser;

    public function setUp(): void
    {
        $this->parser = new Parser(new Tokenizer());
        $this->printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withSpaceIndentation(4)
        );
    }

    public static function dataForIdempotencyTest(): Generator
    {
        $parser = new Parser(new Tokenizer());
        $printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withSpaceIndentation(4)
        );

        $files = glob(__DIR__ . '/Fixtures/*/*.typoscript');

        foreach ($files as $outputFile) {
            $in = file_get_contents($outputFile);
            $ast = $parser->parseString($in);
            $out = new BufferedOutput();

            $printer->printStatements($ast, $out);

            yield [$out->fetch()];
        }
    }

    #[DataProvider('dataForIdempotencyTest')]
    public function testParsingAndPrintingIsIdempotent($inputCode): void
    {
        $ast = $this->parser->parseString($inputCode);
        $out = new BufferedOutput();

        $this->printer->printStatements($ast, $out);

        assertThat($out->fetch(), equalTo($inputCode));
    }
}
