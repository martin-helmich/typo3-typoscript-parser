<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\Traverser;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\Statement;

/**
 * Class Traverser
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\Traverser
 */
class Traverser
{
    /** @var Statement[] */
    private array $statements;

    private AggregatingVisitor $visitors;

    /**
     * @param Statement[] $statements
     */
    public function __construct(array $statements)
    {
        $this->statements = $statements;
        $this->visitors   = new AggregatingVisitor();
    }

    public function addVisitor(Visitor $visitor): void
    {
        $this->visitors->addVisitor($visitor);
    }

    public function walk(): void
    {
        $this->visitors->enterTree($this->statements);
        $this->walkRecursive($this->statements);
        $this->visitors->exitTree($this->statements);
    }

    /**
     * @param Statement[] $statements
     * @return Statement[]
     */
    private function walkRecursive(array $statements): array
    {
        foreach ($statements as $statement) {
            $this->visitors->enterNode($statement);

            if ($statement instanceof NestedAssignment) {
                $statement->statements = $this->walkRecursive($statement->statements);
            } elseif ($statement instanceof ConditionalStatement) {
                $statement->ifStatements   = $this->walkRecursive($statement->ifStatements);
                $statement->elseStatements = $this->walkRecursive($statement->elseStatements);
            }

            $this->visitors->exitNode($statement);
        }
        return $statements;
    }
}
