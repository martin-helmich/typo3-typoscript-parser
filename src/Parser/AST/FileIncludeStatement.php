<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * Include statements that includes a single TypoScript file.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class FileIncludeStatement extends IncludeStatement
{
    /**
     * The name of the file to include.
     */
    public string $filename;

    /**
     * Conditional statement that is attached to this include
     */
    public ?string $condition;

    /**
     * Determines if this statement uses the new @import syntax
     */
    public bool $newSyntax;

    /**
     * Constructs a new include statement.
     *
     * @param string      $filename   The name of the file to include.
     * @param boolean     $newSyntax  Determines if this statement uses the new import syntax
     * @param string|null $condition  Conditional statement that is attached to this include
     * @param int         $sourceLine The original source line.
     */
    public function __construct(string $filename, bool $newSyntax, ?string $condition, int $sourceLine)
    {
        parent::__construct($sourceLine);
        $this->filename  = $filename;
        $this->newSyntax = $newSyntax;
        $this->condition = $condition;
    }
}
