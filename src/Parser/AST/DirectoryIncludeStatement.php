<?php declare(strict_types=1);

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
     */
    public string $directory;

    /**
     * Conditional statement that is attached to this include
     */
    public string|null $condition;

    /**
     * Same as extensions
     *
     * @deprecated Use `extensions` instead
     */
    public string|null $extension = null;

    /**
     * An optional file extension filter. May be NULL.
     */
    public string|null $extensions = null;

    /**
     * Constructs a new directory include statement.
     *
     * @param string      $directory  The directory to include from.
     * @param string|null $extensions The file extension filter. MAY be NULL.
     * @param string|null $condition  Conditional statement that is attached to this include
     * @param int         $sourceLine The original source line.
     *
     * @psalm-suppress DeprecatedProperty
     */
    public function __construct(string $directory, ?string $extensions, ?string $condition, int $sourceLine)
    {
        parent::__construct($sourceLine);

        $this->directory  = $directory;
        $this->extension  = $extensions;
        $this->extensions = $extensions;
        $this->condition  = $condition;
    }
}
