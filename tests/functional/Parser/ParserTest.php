<?php declare(strict_types=1);
namespace Helmich\TypoScriptParser\Tests\Functional\Parser;

use Generator;
use Helmich\TypoScriptParser\Parser\ParseError;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private Parser $parser;

    public function setUp(): void
    {
        $this->parser = new Parser(new Tokenizer());
    }

    public static function dataForParserTest(): array
    {
        $files = glob(__DIR__ . '/Fixtures/*/*.typoscript');
        $testCases = [];
        foreach ($files as $file) {
            $outputFile = str_replace('.typoscript', '.php', $file);

            $output = null;

            if (file_exists($outputFile)) {
                $output = include $outputFile;
            }

            $testCases[str_replace(".typoscript", "", basename($file))] = [$file, $output];
        }

        return $testCases;
    }

    public static function dataForParseErrorTest(): Generator
    {
        yield ["foo {\n    bar = 1"];
        yield ["foo > bar"];
        yield ["foo {\n    [globalString = GP:foo=1]\n    bar =1 \n    [global]\n}"];
        yield ["[globalString = GP:foo=1]\nbar = 1\n[else]\nbar = 2\n[else]\nbar = 3\n[global]"];
        yield ["foo = 1\n}"];
        yield ["foo = 1\n[end]"];
        yield ["foo :="];
        yield ["foo := foobar"];
        yield ["foo <"];
        yield ["foo < hello world"];
    }

    #[DataProvider('dataForParserTest')]
    #[TestDox("Code is parsed into correct AST")]
    public function testCodeIsParsedIntoCorrectAST($inputFile, $expectedAST)
    {
        $ast = $this->parser->parseStream($inputFile);

        // this happens on incomplete test cases
        $isIncompleteTestCase = $expectedAST === null;
        if ($isIncompleteTestCase) {
            $this->markTestIncomplete(var_export($ast, true));
        }

        $this->assertEquals($expectedAST, $ast);
    }

    #[DataProvider('dataForParseErrorTest')]
    public function testBadCodeCausesParserError($inputCode)
    {
        $this->expectException(ParseError::class);
        $this->parser->parseString($inputCode);
    }
}