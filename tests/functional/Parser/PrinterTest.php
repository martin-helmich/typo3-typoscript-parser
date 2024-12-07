<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tests\Functional\Parser;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Parser\Printer\ASTPrinterInterface;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinter;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConfiguration;
use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\assertThat;

class PrinterTest extends TestCase
{
    private ASTPrinterInterface $printer;

    public function setUp(): void
    {
        $this->printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withSpaceIndentation(4)
        );
    }

    public static function dataForPrinterTest(): array
    {
        $files = glob(__DIR__ . '/Fixtures/*/*.typoscript');
        $testCases = [];

        foreach ($files as $outputFile) {
            $ast = null;
            $astFile = str_replace('.typoscript', '.php', $outputFile);

            if (file_exists($astFile)) {
                $ast = include $astFile;
            }

            $exceptionFile = $outputFile . '.print';
            if (file_exists($exceptionFile)) {
                $outputFile = $exceptionFile;
            }

            $output = file_get_contents($outputFile);

            $testCases[str_replace(".typoscript", "", basename($outputFile))] = [$ast, $output];
        }

        return $testCases;
    }

    #[DataProvider('dataForPrinterTest')]
    public function testParsedCodeIsCorrectlyPrinted(array|null $ast, string $expectedOutput): void
    {
        if ($ast === null) {
            $this->markTestIncomplete("no output AST provided");
        }

        $output = new BufferedOutput();
        $this->printer->printStatements($ast, $output);

        $this->assertEquals(trim($expectedOutput), trim($output->fetch()));
    }

    public function testConditionIndentationIsRespected(): void
    {
        $printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withSpaceIndentation(4)
                ->withIndentConditions()
        );

        $ast = [
            new ConditionalStatement(
                "[foo = bar]",
                [new Assignment(new ObjectPath("foo", "foo"), new Scalar("bar"), 2)],
                [new Assignment(new ObjectPath("foo", "foo"), new Scalar("baz"), 4)],
                1
            )
        ];

        $out = new BufferedOutput();

        $printer->printStatements($ast, $out);

        assertThat($out->fetch(), equalTo("[foo = bar]\n    foo = bar\n[else]\n    foo = baz\n[global]\n"));
    }

    #[Test]
    public function conditionsTerminatedWithEndShouldAlsoBePrintedWithEnd()
    {
        $condition = "[foo == 'bar']\n    foo = bar\n[end]\n";

        $parser = new Parser(new Tokenizer());
        $ast = $parser->parseString($condition);
        $out = new BufferedOutput();

        $printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withIndentConditions()
        );
        $printer->printStatements($ast, $out);

        assertThat($out->fetch(), equalTo($condition));
    }
}
