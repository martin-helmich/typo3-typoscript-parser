<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Tokenizer;

use Helmich\TypoScriptParser\Tokenizer\LineGrouper;
use Helmich\TypoScriptParser\Tokenizer\Token;

class LineGrouperTest extends \PHPUnit_Framework_TestCase
{
    public function testTokensAreGroupsByLine()
    {
        $tokens = [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "foo", 1, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 4),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 1, 5),
            new Token(Token::TYPE_WHITESPACE, " ", 1, 6),
            new Token(Token::TYPE_RIGHTVALUE, "bar", 1, 7),
            new Token(Token::TYPE_WHITESPACE, "\n", 1, 10),
            new Token(Token::TYPE_OBJECT_IDENTIFIER, "bar", 2, 1),
            new Token(Token::TYPE_WHITESPACE, " ", 2, 4),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, "=", 2, 5),
            new Token(Token::TYPE_WHITESPACE, " ", 2, 6),
            new Token(Token::TYPE_RIGHTVALUE, "baz", 2, 7),
            new Token(Token::TYPE_WHITESPACE, "\n", 2, 10),
        ];

        $lines = (new LineGrouper($tokens))->getLines();

        assertThat(count($lines), equalTo(2));
        assertThat(count($lines[1]), equalTo(6));
        assertThat(count($lines[2]), equalTo(6));
        assertThat($lines[1][0]->getValue(), equalTo("foo"));
        assertThat($lines[1][4]->getValue(), equalTo("bar"));
        assertThat($lines[2][0]->getValue(), equalTo("bar"));
        assertThat($lines[2][4]->getValue(), equalTo("baz"));
    }
}