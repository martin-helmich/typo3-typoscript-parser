<?php

namespace Helmich\TypoScriptParser\Tests\Unit\Parser\Printer;

use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConfiguration;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\isTrue;

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
        self::assertTrue($prettyPrinterConfiguration->shouldAddClosingGlobal());
    }

    public function testWithEmptyLineBreaks(): void
    {
        $prettyPrinterConfiguration = PrettyPrinterConfiguration::create()->withEmptyLineBreaks();
        self::assertTrue($prettyPrinterConfiguration->shouldIncludeEmptyLineBreaks());
    }

    public function testWithIndentConditions(): void
    {
        $prettyPrinterConfiguration = PrettyPrinterConfiguration::create()->withIndentConditions();
        assertThat($prettyPrinterConfiguration->shouldIndentConditions(), isTrue());
    }
}
