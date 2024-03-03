<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser;

use Helmich\TypoScriptParser\Parser\TokenStream;
use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isTrue;

class TokenStreamTest extends TestCase
{
    private TokenStream $stream;

    /** @var TokenInterface[] */
    private array $tokens;

    public function setUp(): void
    {
        $this->tokens = [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, 'foo', 1, 1),
            new Token(Token::TYPE_WHITESPACE, ' ', 1, 4),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, '=', 1, 5),
            new Token(Token::TYPE_WHITESPACE, ' ', 1, 6),
            new Token(Token::TYPE_RIGHTVALUE, 'bar', 1, 7),
        ];
        $this->stream = new TokenStream($this->tokens);
    }

    public function testCanIterateStream()
    {
        $count = 0;
        foreach ($this->stream as $key => $token) {
            $count++;
            assertThat($token->getType(), equalTo($this->tokens[$key]->getType()));
        }

        assertThat($count, equalTo(count($this->tokens)));
    }

    public function testCanBeAccessedAsArray()
    {
        assertThat(isset($this->stream[1]), isTrue());
        assertThat($this->stream[4]->getValue(), equalTo('bar'));
    }

    public function testCannotSet()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->stream[3] = new Token(Token::TYPE_OPERATOR_COPY, '<', 1, 1);
    }

    public function testCannotAppend()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->stream[] = new Token(Token::TYPE_OPERATOR_COPY, '<', 1, 1);
    }

    public function testCannotUnset()
    {
        $this->expectException(\BadMethodCallException::class);
        unset($this->stream[3]);
    }
}