<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Tokenizer;

use Helmich\TypoScriptParser\Tokenizer\TokenizerException;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

class TokenizerExceptionTest extends TestCase
{
    public function testCanGetSourceLine()
    {
        $exc = new TokenizerException('Foobar', 1234, null, 4312);
        assertThat($exc->getSourceLine(), equalTo(4312));
    }
}