<?php
namespace Helmich\TypoScriptParser\Parser\Traverser;

use Helmich\TypoScriptParser\Parser\AST\Statement;

/**
 * Interface Visitor
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\Traverser
 */
interface Visitor
{
    /**
     * @param array $statements
     * @return void
     */
    public function enterTree(array $statements);

    /**
     * @param Statement $statement
     * @return void
     */
    public function enterNode(Statement $statement);

    /**
     * @param Statement $statement
     * @return void
     */
    public function exitNode(Statement $statement);

    /**
     * @param array $statements
     * @return void
     */
    public function exitTree(array $statements);
}
