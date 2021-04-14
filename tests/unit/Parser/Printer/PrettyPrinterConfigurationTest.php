<?php

namespace Helmich\TypoScriptParser\Tests\Unit\Parser\Printer;

use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinterConfiguration;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PrettyPrinterConfigurationTest extends TestCase
{
    public function testWrongIndentationStyleThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PrettyPrinterConfiguration(false, false, 4, 'foo');
    }

    public function testTabsWithIndentationSizeThrowsException(): void
    {
        $this->expectException(LogicException::class);
        new PrettyPrinterConfiguration(false, false, 4, PrettyPrinterConfiguration::INDENTATION_STYLE_TABS);
    }

    public function testTabsIndentationStyle(): void
    {
        $prettyPrinterConfiguration = new PrettyPrinterConfiguration(false, false, 0, PrettyPrinterConfiguration::INDENTATION_STYLE_TABS);
        self::assertSame("\t", $prettyPrinterConfiguration->getIndentation());
    }

    public function testSpacesIndentationStyle(): void
    {
        $prettyPrinterConfiguration = new PrettyPrinterConfiguration(false, false, 4, PrettyPrinterConfiguration::INDENTATION_STYLE_SPACES);
        self::assertSame("    ", $prettyPrinterConfiguration->getIndentation());
    }
}
