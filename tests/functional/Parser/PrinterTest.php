<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tests\Functional\Parser;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Scalar;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Parser\Printer\ASTPrinterInterface;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinter;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConditionTermination;
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConfiguration;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

class PrinterTest extends TestCase
{
    private ASTPrinterInterface $printer;

    public function setUp(): void
    {
        $this->printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withSpaceIndentation(4)
                ->withIndentConditions()
        );
    }

    /**
     * @return array<string, array{Statement[]|null, string}>
     */
    public static function dataForPrinterTest(): array
    {
        $files = glob(__DIR__ . '/Fixtures/*/*.typoscript');

        assert($files !== false);

        $testCases = [];
        foreach ($files as $outputFile) {
            $ast = null;
            $astFile = str_replace('.typoscript', '.php', $outputFile);

            if (file_exists($astFile)) {
                /** @var Statement[] $ast */
                $ast = include $astFile;
            }

            $exceptionFile = $outputFile . '.print';
            if (file_exists($exceptionFile)) {
                $outputFile = $exceptionFile;
            }

            $output = file_get_contents($outputFile);
            assert($output !== false);

            $testGroup = basename(dirname($outputFile));
            $testCases[$testGroup . " / " . str_replace(".typoscript", "", basename($outputFile))] = [$ast, $output];
        }

        return $testCases;
    }

    /**
     * @param Statement[]|null $ast
     */
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
    public function conditionsTerminatedWithEndShouldDefaultToPrinterDefaults()
    {
        $condition = "[foo == 'bar']\n    foo = bar\n[end]\n";
        $expectedOutput = "[foo == 'bar']\n    foo = bar\n[global]\n";

        $parser = new Parser(new Tokenizer());
        $ast = $parser->parseString($condition);
        $out = new BufferedOutput();

        $printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withIndentConditions()
        );
        $printer->printStatements($ast, $out);

        assertThat($out->fetch(), equalTo($expectedOutput));
    }

    public static function conditionTerminations(): array
    {
        return [["[end]"], ["[global]"]];
    }

    #[Test]
    #[DataProvider("conditionTerminations")]
    public function conditionsTerminatedWithEndShouldAlsoBePrintedWithEndWhenConfigured(string $termination)
    {
        $condition = "[foo == 'bar']\n    foo = bar\n$termination\n";

        $parser = new Parser(new Tokenizer());
        $ast = $parser->parseString($condition);
        $out = new BufferedOutput();

        $printer = new PrettyPrinter(
            PrettyPrinterConfiguration::create()
                ->withEmptyLineBreaks()
                ->withIndentConditions()
                ->withConditionTermination(PrettyPrinterConditionTermination::Keep)
        );
        $printer->printStatements($ast, $out);

        assertThat($out->fetch(), equalTo($condition));
    }
}
