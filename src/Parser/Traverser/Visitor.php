<?php declare(strict_types=1);

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
    public function enterTree(array $statements): void;

    /**
     * @param Statement $statement
     * @return void
     */
    public function enterNode(Statement $statement): void;

    /**
     * @param Statement $statement
     * @return void
     */
    public function exitNode(Statement $statement): void;

    /**
     * @param array $statements
     * @return void
     */
    public function exitTree(array $statements): void;
}
