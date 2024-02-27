<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\Printer;

use Helmich\TypoScriptParser\Parser\AST\Statement;
use Symfony\Component\Console\Output\OutputInterface;

interface ASTPrinterInterface
{
    /**
     * @param Statement[] $statements
     */
    public function printStatements(array $statements, OutputInterface $output): void;

    public function setPrettyPrinterConfiguration(PrettyPrinterConfiguration $prettyPrinterConfiguration): void;
}
