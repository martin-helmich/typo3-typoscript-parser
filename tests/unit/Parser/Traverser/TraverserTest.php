<?php
namespace Helmich\TypoScriptParser\Tests\Unit\Parser\Traverser;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\ScalarValue;
use Helmich\TypoScriptParser\Parser\Traverser\Traverser;
use Helmich\TypoScriptParser\Parser\Traverser\Visitor;
use PHPUnit\Framework\TestCase;

class TraverserTest extends TestCase
{
    private $tree;

    /** @var Traverser */
    private $traverser;

    public function setUp(): void
    {
        $this->tree = [
            new Assignment(
                new ObjectPath('foo', 'foo'),
                new ScalarValue('bar'),
                1
            ),
            new ConditionalStatement(
                '[globalVar = GP:foo=1]',
                [new Assignment(new ObjectPath('foo', 'foo'), new ScalarValue('bar'), 2)],
                [new Assignment(new ObjectPath('foo', 'foo'), new ScalarValue('baz'), 4)],
                2
            ),
            new NestedAssignment(
                new ObjectPath('bar', 'bar'),
                [new Assignment(new ObjectPath('bar.baz', 'baz'), new ScalarValue('foo'), 1)],
                3
            )
        ];

        $this->traverser = new Traverser($this->tree);
    }

    public function testHookFunctionsAreCalled()
    {
        $visitor = $this->prophesize(Visitor::class);
        $visitor->enterTree($this->tree)->shouldBeCalled();
        $visitor->exitTree($this->tree)->shouldBeCalled();
        $visitor->enterNode($this->tree[0])->shouldBeCalled();
        $visitor->enterNode($this->tree[1])->shouldBeCalled();
        $visitor->enterNode($this->tree[1]->ifStatements[0])->shouldBeCalled();
        $visitor->enterNode($this->tree[1]->elseStatements[0])->shouldBeCalled();
        $visitor->enterNode($this->tree[2])->shouldBeCalled();
        $visitor->enterNode($this->tree[2]->statements[0])->shouldBeCalled();
        $visitor->exitNode($this->tree[0])->shouldBeCalled();
        $visitor->exitNode($this->tree[1])->shouldBeCalled();
        $visitor->exitNode($this->tree[1]->ifStatements[0])->shouldBeCalled();
        $visitor->exitNode($this->tree[1]->elseStatements[0])->shouldBeCalled();
        $visitor->exitNode($this->tree[2])->shouldBeCalled();
        $visitor->exitNode($this->tree[2]->statements[0])->shouldBeCalled();

        $this->traverser->addVisitor($visitor->reveal());
        $this->traverser->walk();
    }
}