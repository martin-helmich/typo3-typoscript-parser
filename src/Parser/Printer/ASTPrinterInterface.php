<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser\Printer;

use Symfony\Component\Console\Output\OutputInterface;

interface ASTPrinterInterface
{
    /**
     * @param \Helmich\TypoScriptParser\Parser\AST\Statement[]  $statements
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    public function printStatements(array $statements, OutputInterface $output): void;
}
