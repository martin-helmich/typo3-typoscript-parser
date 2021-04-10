<?php
declare(strict_types=1);


namespace Helmich\TypoScriptParser\Parser\Printer;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * PrinterConfiguration
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\PrettyPrinterConfiguration
 */
final class PrettyPrinterConfiguration
{
    /**
     * @var string
     */
    public const INDENTATION_STYLE_SPACES = 'spaces';

    /**
     * @var string
     */
    public const INDENTATION_STYLE_TABS = 'tabs';
    const ALLOWED_INDENTATION_STYLES = [self::INDENTATION_STYLE_TABS, self::INDENTATION_STYLE_SPACES];

    /**
     * @var bool
     */
    private $addClosingGlobal;

    /**
     * @var bool
     */
    private $includeEmptyLineBreaks;

    /**
     * @var int
     */
    private $indentationSize;

    /**
     * @var string
     */
    private $indentationStyle;

    public function __construct(bool $addClosingGlobal, bool $includeEmptyLineBreaks, int $indentationSize, string $indentationStyle)
    {
        if(!in_array($indentationStyle, self::ALLOWED_INDENTATION_STYLES)) {
            throw new InvalidArgumentException(
                sprintf('Indentation style must be one of %s but got %s',
                    implode(',', self::ALLOWED_INDENTATION_STYLES),
                    $indentationStyle
                )
            );
        }

        $this->addClosingGlobal = $addClosingGlobal;
        $this->includeEmptyLineBreaks = $includeEmptyLineBreaks;
        $this->indentationSize = $indentationSize;
        $this->indentationStyle = $indentationStyle;
    }

    public static function getDefault(): self
    {
        return new self(false, false, 4, self::INDENTATION_STYLE_SPACES);
    }

    public function isAddClosingGlobal(): bool
    {
        return $this->addClosingGlobal;
    }

    public function isIncludeEmptyLineBreaks(): bool
    {
        return $this->includeEmptyLineBreaks;
    }

    public function getIndentation(): string
    {
        if($this->indentationStyle === self::INDENTATION_STYLE_TABS) {
            return "\t";
        }

        return str_repeat(' ', $this->indentationSize);
    }
}