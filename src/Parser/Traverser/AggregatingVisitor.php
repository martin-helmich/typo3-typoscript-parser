<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\Traverser;

use Helmich\TypoScriptParser\Parser\AST\Statement;

/**
 * Class AggregatingVisitor
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\Traverser
 */
class AggregatingVisitor implements Visitor
{
    /** @var Visitor[] */
    private array $visitors = [];

    public function addVisitor(Visitor $visitor): void
    {
        $this->visitors[spl_object_hash($visitor)] = $visitor;
    }

    /**
     * @param Statement[] $statements
     */
    public function enterTree(array $statements): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->enterTree($statements);
        }
    }

    public function enterNode(Statement $statement): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->enterNode($statement);
        }
    }

    public function exitNode(Statement $statement): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->exitNode($statement);
        }
    }

    /**
     * @param Statement[] $statements
     */
    public function exitTree(array $statements): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->exitTree($statements);
        }
    }
}
