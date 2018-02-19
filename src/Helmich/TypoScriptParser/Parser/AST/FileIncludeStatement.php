<?php

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
     *
     * @var string
     */
    public $filename;

    /**
     * Determines if this statement uses the new @import syntax
     *
     * @var boolean
     */
    public $newSyntax;

    /**
     * Constructs a new include statement.
     *
     * @param string  $filename   The name of the file to include.
     * @param boolean $newSyntax  Determines if this statement uses the new import syntax
     * @param int     $sourceLine The original source line.
     */
    public function __construct($filename, $newSyntax, $sourceLine)
    {
        parent::__construct($sourceLine);
        $this->filename = $filename;
        $this->newSyntax = $newSyntax;
    }
}
