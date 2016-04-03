<?php
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
    private $visitors = [];

    /**
     * @param Visitor $visitor
     * @return void
     */
    public function addVisitor(Visitor $visitor)
    {
        $this->visitors[spl_object_hash($visitor)] = $visitor;
    }

    /**
     * @param array $statements
     * @return void
     */
    public function enterTree(array $statements)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->enterTree($statements);
        }
    }

    /**
     * @param Statement $statement
     * @return void
     */
    public function enterNode(Statement $statement)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->enterNode($statement);
        }
    }

    /**
     * @param Statement $statement
     * @return void
     */
    public function exitNode(Statement $statement)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->exitNode($statement);
        }
    }

    /**
     * @param array $statements
     * @return void
     */
    public function exitTree(array $statements)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->exitTree($statements);
        }
    }
}
