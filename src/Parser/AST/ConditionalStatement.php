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
     * @deprecated Use $terminator instead
     */
    public bool $unterminated = false;

    /**
     * Indicates how (and if) the conditional statement was terminated with a
     * [global] or [end] statement.
     *
     * This information is not that important for parsing, but might be of
     * interest to linters and printers.
     */
    public ConditionalStatementTerminator $terminator = ConditionalStatementTerminator::Global;

    /**
     * Constructs a conditional statement.
     *
     * @param string $condition The condition statement
     * @param Statement[] $ifStatements The statements in the if-branch.
     * @param Statement[] $elseStatements The statements in the else-branch (may be empty).
     * @param ConditionalStatementTerminator $terminator An indicator as to how the statement was termianted
     * @param int $sourceLine The original source line.
     */
    public function __construct(string $condition, array $ifStatements, array $elseStatements, int $sourceLine, ConditionalStatementTerminator $terminator = ConditionalStatementTerminator::Global)
    {
        parent::__construct($sourceLine);

        $this->condition = $condition;
        $this->ifStatements = $ifStatements;
        $this->elseStatements = $elseStatements;
        $this->terminator = $terminator;
        $this->unterminated = $terminator === ConditionalStatementTerminator::Unterminated;
    }

    public function getSubNodeNames(): array
    {
        return ['condition', 'ifStatements', 'elseStatements', 'terminator'];
    }
}
