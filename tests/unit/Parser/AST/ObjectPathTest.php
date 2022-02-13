<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tests\Unit\Parser\AST;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\identicalTo;

class ObjectPathTest extends TestCase
{
    public function testPathsRemainStrings()
    {
        $op = new ObjectPath("foo.0", "0");
        assertThat($op->relativeName, identicalTo("0"));
    }

    public function testIntPathsRaisesTypeError()
    {
        $this->expectException(\TypeError::class);
        new ObjectPath("foo.0", 0);
    }
}