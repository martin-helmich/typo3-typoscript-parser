<?php

declare(strict_types=1);


namespace Helmich\TypoScriptParser\Parser\Printer;

/**
 * PrinterConfiguration
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\PrettyPrinterConfiguration
 */
final class PrettyPrinterConfiguration
{
    public const INDENTATION_STYLE_SPACES = 'spaces';

    public const INDENTATION_STYLE_TABS = 'tabs';

    private bool $addClosingGlobal = false;

    private bool $includeEmptyLineBreaks = false;

    private int $indentationSize = 4;

    private string $indentationStyle = self::INDENTATION_STYLE_SPACES;

    private bool $indentConditions = false;

    /**
     * Determines how conditions should be terminated.
     *
     * NOTE: Using EnforceEnd would be preferable, but we're keeping
     * EnforceGlobal as default due to backwards-compatibility.
     */
    private PrettyPrinterConditionTermination $conditionTermination = PrettyPrinterConditionTermination::EnforceGlobal;

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function withTabs(): self
    {
        $clone = clone $this;
        $clone->indentationStyle = self::INDENTATION_STYLE_TABS;
        $clone->indentationSize = 1;

        return $clone;
    }

    public function withSpaceIndentation(int $size): self
    {
        $clone = clone $this;
        $clone->indentationStyle = self::INDENTATION_STYLE_SPACES;
        $clone->indentationSize = $size;

        return $clone;
    }

    public function withClosingGlobalStatement(): self
    {
        $clone = clone $this;
        $clone->addClosingGlobal = true;

        return $clone;
    }

    public function withEmptyLineBreaks(): self
    {
        $clone = clone $this;
        $clone->includeEmptyLineBreaks = true;

        return $clone;
    }

    public function withIndentConditions(): self
    {
        $clone = clone $this;
        $clone->indentConditions = true;

        return $clone;
    }

    public function withConditionTermination(PrettyPrinterConditionTermination $termination): self
    {
        $clone = clone $this;
        $clone->conditionTermination = $termination;

        return $clone;
    }

    public function shouldAddClosingGlobal(): bool
    {
        return $this->addClosingGlobal;
    }

    public function shouldIncludeEmptyLineBreaks(): bool
    {
        return $this->includeEmptyLineBreaks;
    }

    public function shouldIndentConditions(): bool
    {
        return $this->indentConditions;
    }

    public function getIndentation(): string
    {
        if ($this->indentationStyle === self::INDENTATION_STYLE_TABS) {
            return "\t";
        }

        return str_repeat(' ', $this->indentationSize);
    }

    public function getConditionTermination(): PrettyPrinterConditionTermination
    {
        return $this->conditionTermination;
    }
}
