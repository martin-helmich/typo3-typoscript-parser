<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST\Operator;

use Helmich\TypoScriptParser\Parser\AST\Node;

/**
 * A modification call (usually on the right-hand side of a modification statement).
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST\Operator
 */
class ModificationCall implements Node
{
    /**
     * The method name.
     */
    public string $method;

    /**
     * The argument list.
     */
    public string $arguments;

    /**
     * Modification call constructor.
     *
     * @param string $method    The method name.
     * @param string $arguments The argument list.
     */
    public function __construct(string $method, string $arguments)
    {
        $this->arguments = $arguments;
        $this->method    = $method;
    }

    /**
     * @return string[]
     */
    public function getSubNodeNames(): array
    {
        return ['arguments', 'method'];
    }
}
