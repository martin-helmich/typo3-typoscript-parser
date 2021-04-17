<?php

namespace Helmich\TypoScriptParser\Tests\Unit\Parser\Printer;

use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConfiguration;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PrettyPrinterConfigurationTest extends TestCase
{
    public function testTabsIndentationStyle(): void
    {
        $prettyPrinterConfiguration = PrettyPrinterConfiguration::create()->withTabs();
        self::assertSame("\t", $prettyPrinterConfiguration->getIndentation());
    }

    public function testSpacesIndentationStyle(): void
    {
        $prettyPrinterConfiguration = PrettyPrinterConfiguration::create()->withSpaceIndentation(4);
        self::assertSame("    ", $prettyPrinterConfiguration->getIndentation());
    }

    public function testWithGlobalStatement(): void
    {
        $prettyPrinterConfiguration = PrettyPrinterConfiguration::create()->withClosingGlobalStatement();
        self::assertTrue($prettyPrinterConfiguration->isAddClosingGlobal());
    }

    public function testWithEmptyLineBreaks(): void
    {
        $prettyPrinterConfiguration = PrettyPrinterConfiguration::create()->withEmptyLineBreaks();
        self::assertTrue($prettyPrinterConfiguration->isIncludeEmptyLineBreaks());
    }
}
