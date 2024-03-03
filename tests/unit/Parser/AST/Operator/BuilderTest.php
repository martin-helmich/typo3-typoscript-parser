<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser\AST\Operator;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Builder as OperatorBuilder;
use Helmich\TypoScriptParser\Parser\AST\Operator\Copy;
use Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation;
use Helmich\TypoScriptParser\Parser\AST\Scalar;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\identicalTo;
use function PHPUnit\Framework\isInstanceOf;

class BuilderTest extends TestCase
{
    private OperatorBuilder $opBuilder;

    public function setUp(): void
    {
        $this->opBuilder = new OperatorBuilder();
    }

    public function testObjectCreationIsBuilt()
    {
        $op = $this->opBuilder->objectCreation(
            $foo = new ObjectPath('foo', 'foo'),
            $text = new Scalar('TEXT'),
            1
        );

        assertThat($op, isInstanceOf(ObjectCreation::class));
        assertThat($op->object, identicalTo($foo));
        assertThat($op->value, identicalTo($text));
    }

    public function testCopyOperatorIsBuilt()
    {
        $op = $this->opBuilder->copy(
            $foo = new ObjectPath('foo', 'foo'),
            $bar = new ObjectPath('bar', 'bar'),
            1
        );

        assertThat($op, isInstanceOf(Copy::class));
        assertThat($op->object, identicalTo($foo));
        assertThat($op->target, identicalTo($bar));
    }

    public function testPassesExcessParameters()
    {
        $op = $this->opBuilder->copy(
            $foo = new ObjectPath('foo', 'foo'),
            $bar = new ObjectPath('bar', 'bar'),
            1,
            'foo'
        );

        assertThat($op, isInstanceOf(Copy::class));
        assertThat($op->object, identicalTo($foo));
        assertThat($op->target, identicalTo($bar));
    }
}
