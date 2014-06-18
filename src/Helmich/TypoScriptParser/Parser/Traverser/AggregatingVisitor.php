<?php
namespace Helmich\TypoScriptParser\Parser\Traverser;


use Helmich\TypoScriptParser\Parser\AST\Statement;

class AggregatingVisitor implements Visitor
{



    /** @var \Helmich\TypoScriptParser\Parser\Traverser\Visitor[] */
    private $visitors = [];



    public function addVisitor(Visitor $visitor)
    {
        $this->visitors[spl_object_hash($visitor)] = $visitor;
    }



    public function enterTree(array $statements)
    {
        foreach ($this->visitors as $visitor)
        {
            $visitor->enterTree($statements);
        }
    }



    public function enterNode(Statement $statement)
    {
        foreach ($this->visitors as $visitor)
        {
            $visitor->enterNode($statement);
        }
    }



    public function exitNode(Statement $statement)
    {
        foreach ($this->visitors as $visitor)
        {
            $visitor->exitNode($statement);
        }
    }



    public function exitTree(array $statements)
    {
        foreach ($this->visitors as $visitor)
        {
            $visitor->exitTree($statements);
        }
    }
}