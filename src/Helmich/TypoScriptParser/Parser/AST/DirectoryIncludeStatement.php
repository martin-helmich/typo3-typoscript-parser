<?php
namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * Include statements that includes many TypoScript files from a directory.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class DirectoryIncludeStatement extends IncludeStatement
{

    /**
     * The directory to include from.
     *
     * @var string
     */
    public $directory;

    /**
     * Same as extensions
     *
     * @var string
     * @deprecated Use `extensions` instead
     */
    public $extension = null;

    /**
     * An optional file extension filter. May be NULL.
     *
     * @var string
     */
    public $extensions = null;

    /**
     * Constructs a new directory include statement.
     *
     * @param string $directory  The directory to include from.
     * @param string $extensions The file extension filter. MAY be NULL.
     * @param int    $sourceLine The original source line.
     */
    public function __construct($directory, $extensions, $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->directory  = $directory;
        $this->extension  = $extensions;
        $this->extensions = $extensions;
    }
}