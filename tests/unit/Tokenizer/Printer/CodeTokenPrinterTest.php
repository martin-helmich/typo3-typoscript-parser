<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Tokenizer\Printer;

use Helmich\TypoScriptParser\Tokenizer\Printer\CodeTokenPrinter;
use Helmich\TypoScriptParser\Tokenizer\Token;

class CodeTokenPrinterTest extends \PHPUnit_Framework_TestCase
{
    /** @var CodeTokenPrinter */
    private $printer;

    public function setUp()
    {
        $this->printer = new CodeTokenPrinter();
    }

    public function testTokensArePrinted()
    {
        $expectedOutput = <<<OUT
foo = bar
bar = bar
OUT;
        $tokens = [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1),
            new Token(Token::TYPE_RIGHTVALUE, "bar", 1),
            new Token(Token::TYPE_WHITESPACE, "\n", 1),
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "bar", 2),
            new Token(Token::TYPE_WHITESPACE, " ", 2),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 2),
            new Token(Token::TYPE_WHITESPACE, " ", 2),
            new Token(Token::TYPE_RIGHTVALUE, "bar", 2),
            new Token(Token::TYPE_WHITESPACE, "\n", 2),
        ];

        $output = $this->printer->printTokenStream($tokens);
        assertThat(trim($output), equalTo(trim($expectedOutput)));
    }
}