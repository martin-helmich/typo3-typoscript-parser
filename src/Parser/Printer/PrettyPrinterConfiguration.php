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
    /**
     * @var bool
     */
    private $addClosingGlobal;

    /**
     * @var bool
     */
    private $includeEmptyLineBreaks;

    public function __construct(bool $addClosingGlobal, bool $includeEmptyLineBreaks)
    {
        $this->addClosingGlobal = $addClosingGlobal;
        $this->includeEmptyLineBreaks = $includeEmptyLineBreaks;
    }

    public function isAddClosingGlobal(): bool
    {
        return $this->addClosingGlobal;
    }

    public function isIncludeEmptyLineBreaks(): bool
    {
        return $this->includeEmptyLineBreaks;
    }
}