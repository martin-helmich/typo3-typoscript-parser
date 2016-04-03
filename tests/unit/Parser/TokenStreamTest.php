<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser;

use Helmich\TypoScriptParser\Parser\TokenStream;
use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

class TokenStreamTest extends \PHPUnit_Framework_TestCase
{
    /** @var tokenStream */
    private $stream;

    /** @var TokenInterface[] */
    private $tokens;

    public function setUp()
    {
        $this->tokens = [
            new Token(Token::TYPE_OBJECT_IDENTIFIER, 'foo', 1),
            new Token(Token::TYPE_WHITESPACE, ' ', 1),
            new Token(Token::TYPE_OPERATOR_ASSIGNMENT, '=', 1),
            new Token(Token::TYPE_WHITESPACE, ' ', 1),
            new Token(Token::TYPE_RIGHTVALUE, 'bar', 1),
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

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCannotSet()
    {
        $this->stream[3] = new Token(Token::TYPE_OPERATOR_COPY, '<', 1);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCannotAppend()
    {
        $this->stream[] = new Token(Token::TYPE_OPERATOR_COPY, '<', 1);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCannotUnset()
    {
        unset($this->stream[3]);
    }
}