<?php

namespace Helmich\TypoScriptParser\Parser\Printer;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement;
use Helmich\TypoScriptParser\Parser\AST\FileIncludeStatement;
use Helmich\TypoScriptParser\Parser\AST\IncludeStatement;
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\BinaryObjectOperator;
use Helmich\TypoScriptParser\Parser\AST\Operator\Copy;
use Helmich\TypoScriptParser\Parser\AST\Operator\Delete;
use Helmich\TypoScriptParser\Parser\AST\Operator\Modification;
use Helmich\TypoScriptParser\Parser\AST\Operator\Reference;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Printer class that generates TypoScript code from an AST
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\Printer
 */
class PrettyPrinter implements ASTPrinterInterface
{
    /**
     * @param Statement[]     $statements
     * @param OutputInterface $output
     * @return string
     */
    public function printStatements(array $statements, OutputInterface $output)
    {
        $this->printStatementList($statements, $output, 0);
    }

    /**
     * @param Statement[]     $statements
     * @param OutputInterface $output
     * @param int             $nesting
     * @return string
     */
    private function printStatementList(array $statements, OutputInterface $output, $nesting = 0)
    {
        $indent = $this->getIndent($nesting);
        $count = count($statements);
        //foreach ($statements as $statement) {
        for ($i = 0; $i < $count; $i++) {
            $statement = $statements[$i];

            if ($statement instanceof NestedAssignment) {
                $this->printNestedAssignment($output, $nesting, $statement);
            } elseif ($statement instanceof Assignment) {
                $this->printAssignment($output, $statement, $indent);
            } elseif ($statement instanceof BinaryObjectOperator) {
                $this->printBinaryObjectOperator($output, $statement, $nesting);
            } elseif ($statement instanceof Delete) {
                $output->writeln($indent . $statement->object->relativeName . ' >');
            } elseif ($statement instanceof Modification) {
                $output->writeln(
                    sprintf(
                        "%s%s := %s(%s)",
                        $indent,
                        $statement->object->relativeName,
                        $statement->call->method,
                        $statement->call->arguments
                    )
                );
            } elseif ($statement instanceof ConditionalStatement) {
                $next = $i + 1 < $count ? $statements[$i + 1] : null;
                $previous = $i - 1 >= 0 ? $statements[$i - 1] : null;

                $this->printConditionalStatement(
                    $output,
                    $nesting,
                    $statement,
                    $next instanceof ConditionalStatement,
                    $previous instanceof ConditionalStatement
                );
            } elseif ($statement instanceof IncludeStatement) {
                $this->printIncludeStatement($output, $statement);
            }
        }
    }

    private function getIndent($nesting)
    {
        return str_repeat('    ', $nesting);
    }

    private function printBinaryObjectOperator(OutputInterface $output, BinaryObjectOperator $operator, $nesting)
    {
        $targetObjectPath = $operator->target->relativeName;

        if ($operator instanceof Copy) {
            $output->writeln($this->getIndent($nesting) . $operator->object->relativeName . ' < ' . $targetObjectPath);
        } elseif ($operator instanceof Reference) {
            $output->writeln($this->getIndent($nesting) . $operator->object->relativeName . ' =< ' . $targetObjectPath);
        }
    }

    private function printIncludeStatement(OutputInterface $output, IncludeStatement $statement)
    {
        if ($statement instanceof FileIncludeStatement) {
            $this->printFileIncludeStatement($output, $statement);
        } elseif ($statement instanceof DirectoryIncludeStatement) {
            $this->printDirectoryIncludeStatement($output, $statement);
        }
    }

    private function printFileIncludeStatement(OutputInterface $output, FileIncludeStatement $statement)
    {
        if ($statement->newSyntax) {
            $output->writeln('@import \'' . $statement->filename . '\'');
        } else {
            $output->writeln('<INCLUDE_TYPOSCRIPT: source="FILE:' . $statement->filename . '">');
        }
    }

    private function printDirectoryIncludeStatement(OutputInterface $output, DirectoryIncludeStatement $statement)
    {
        $includeStmt = '<INCLUDE_TYPOSCRIPT: source="DIR:' . $statement->directory . '">';
        if ($statement->extensions) {
            $includeStmt = '<INCLUDE_TYPOSCRIPT: source="DIR:' . $statement->directory . '" extensions="' . $statement->extensions . '">';
        }

        $output->writeln($includeStmt);
    }

    /**
     * @param OutputInterface  $output
     * @param int              $nesting
     * @param NestedAssignment $statement
     */
    private function printNestedAssignment(OutputInterface $output, $nesting, NestedAssignment $statement)
    {
        $output->writeln($this->getIndent($nesting) . $statement->object->relativeName . ' {');
        $this->printStatementList($statement->statements, $output, $nesting + 1);
        $output->writeln($this->getIndent($nesting) . '}');
    }

    /**
     * @param OutputInterface      $output
     * @param int                  $nesting
     * @param ConditionalStatement $statement
     * @param bool                 $hasNext
     * @param bool                 $hasPrevious
     */
    private function printConditionalStatement(OutputInterface $output, $nesting, $statement, $hasNext = false, $hasPrevious = false)
    {
        if (!$hasPrevious) {
            $output->writeln('');
        }

        $output->writeln($statement->condition);
        $this->printStatementList($statement->ifStatements, $output, $nesting);

        if (count($statement->elseStatements) > 0) {
            $output->writeln('[else]');
            $this->printStatementList($statement->elseStatements, $output, $nesting);
        }

        if (!$hasNext) {
            $output->writeln('[global]');
        }
    }

    /**
     * @param OutputInterface $output
     * @param Assignment      $statement
     * @param int             $indent
     */
    private function printAssignment(OutputInterface $output, Assignment $statement, $indent)
    {
        if (strpos($statement->value->value, "\n") !== false) {
            $output->writeln($indent . $statement->object->relativeName . ' (');
            $output->writeln(rtrim($statement->value->value));
            $output->writeln($indent . ')');
            return;
        }

        $output->writeln($indent . $statement->object->relativeName . ' = ' . $statement->value->value);
    }
}
