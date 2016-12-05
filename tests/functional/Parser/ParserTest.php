<?php
namespace Helmich\TypoScriptParser\Tests\Functional\Parser;

use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var Parser */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser(new Tokenizer());
    }

    public function dataForParserTest()
    {
        $files = glob(__DIR__ . '/Fixtures/*/*.typoscript');
        foreach ($files as $file) {
            $outputFile = str_replace('.typoscript', '.php', $file);
            $output     = include $outputFile;

            yield [$file, $output];
        }
    }

    public function dataForParseErrorTest()
    {
        yield ["foo {\n    bar = 1"];
        yield ["foo > bar"];
        yield ["foo {\n    [globalString = GP:foo=1]\n    bar =1 \n    [global]\n}"];
        yield ["[globalString = GP:foo=1]\nbar = 1\n[else]\nbar = 2\n[else]\nbar = 3\n[global]"];
        //yield ["[globalString = GP:foo=1]\nbar = 1\n[else][else]\nbar = 3\n[global]"];
        yield ["foo = 1\n}"];
        yield ["foo = 1\n[end]"];
        yield ["foo :="];
        yield ["foo := foobar"];
        yield ["foo <"];
        yield ["foo < hello world"];
    }

    /**
     * @dataProvider dataForParserTest
     * @param $inputFile
     * @param $expectedAST
     */
    public function testCodeIsParsedIntoCorrectAST($inputFile, $expectedAST)
    {
        $ast = $this->parser->parseStream($inputFile);
        $this->assertEquals($expectedAST, $ast);
    }

    /**
     * @dataProvider dataForParseErrorTest
     * @expectedException \Helmich\TypoScriptParser\Parser\ParseError
     * @param $inputCode
     */
    public function testBadCodeCausesParserError($inputCode)
    {
        $this->parser->parseString($inputCode);
    }
}