<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * A conditional statement with a condition, an if-branch and an optional else-branch.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class ConditionalStatement extends Statement
{

    /**
     * The condition to evaluate.
     */
    public string $condition;

    /**
     * Statements within the if-branch.
     *
     * @var Statement[]
     */
    public array $ifStatements = [];

    /**
     * Statements within the else-branch.
     *
     * @var Statement[]
     */
    public array $elseStatements = [];

    /**
     * Constructs a conditional statement.
     *
     * @param string      $condition      The condition statement
     * @param Statement[] $ifStatements   The statements in the if-branch.
     * @param Statement[] $elseStatements The statements in the else-branch (may be empty).
     * @param int         $sourceLine     The original source line.
     */
    public function __construct(string $condition, array $ifStatements, array $elseStatements, int $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->condition      = $condition;
        $this->ifStatements   = $ifStatements;
        $this->elseStatements = $elseStatements;
    }
}
