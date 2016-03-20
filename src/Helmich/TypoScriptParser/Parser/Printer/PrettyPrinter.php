<?php
namespace Helmich\TypoScriptParser\Parser\Printer;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement;
use Helmich\TypoScriptParser\Parser\AST\FileIncludeStatement;
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\BinaryObjectOperator;
use Helmich\TypoScriptParser\Parser\AST\Operator\Copy;
use Helmich\TypoScriptParser\Parser\AST\Operator\Delete;
use Helmich\TypoScriptParser\Parser\AST\Operator\Modification;
use Helmich\TypoScriptParser\Parser\AST\Operator\Reference;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Symfony\Component\Console\Output\OutputInterface;

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
        foreach ($statements as $statement) {
            if ($statement instanceof NestedAssignment) {
                $this->printNestedAssignment($output, $nesting, $statement);
                continue;
            }

            if ($statement instanceof Assignment) {
                if (strpos($statement->value->value, "\n") !== false) {
                    $output->writeln($indent . $statement->object->relativeName . ' (');
                    $output->writeln(rtrim($statement->value->value));
                    $output->writeln($indent . ')');
                } else {
                    $output->writeln($indent . $statement->object->relativeName . ' = ' . $statement->value->value);
                }
            } else if ($statement instanceof BinaryObjectOperator) {
                $this->printBinaryObjectOperator($statement, $output, $nesting);
            } else if ($statement instanceof Delete) {
                $output->writeln($indent . $statement->object->relativeName . ' >');
            } else if ($statement instanceof Modification) {
                $output->writeln($indent . $statement->object->relativeName . ' := ' . $statement->call->method . '(' . $statement->call->arguments . ')');
            } else if ($statement instanceof ConditionalStatement) {
                $this->printConditionalStatement($output, $nesting, $statement);
            } else if ($statement instanceof FileIncludeStatement) {
                $output->writeln('<INCLUDE_TYPOSCRIPT: source="FILE:' . $statement->filename . '">');
            } else if ($statement instanceof DirectoryIncludeStatement) {
                $includeStmt = '<INCLUDE_TYPOSCRIPT: source="DIR:' . $statement->directory . '">';
                if ($statement->extensions) {
                    $includeStmt = '<INCLUDE_TYPOSCRIPT: source="DIR:' . $statement->directory . '" extensions="' . $statement->extensions . '">';
                }

                $output->writeln($includeStmt);
            }
        }
    }

    private function getIndent($nesting)
    {
        return str_repeat('    ', $nesting);
    }

    private function printBinaryObjectOperator(BinaryObjectOperator $operator, OutputInterface $output, $nesting)
    {
        $sourceObjectPath = explode('.', $operator->object->absoluteName);
        array_pop($sourceObjectPath);
        $sourceObjectNamespace = implode('.', $sourceObjectPath);

        $targetObjectPath = str_replace($sourceObjectNamespace, '', $operator->target->absoluteName);

        if ($operator instanceof Copy) {
            $output->writeln($this->getIndent($nesting) . $operator->object->relativeName . ' < ' . $targetObjectPath);
        } elseif ($operator instanceof Reference) {
            $output->writeln($this->getIndent($nesting) . $operator->object->relativeName . ' <= ' . $targetObjectPath);
        }
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
     */
    private function printConditionalStatement(OutputInterface $output, $nesting, $statement)
    {
        $output->writeln('');
        $output->writeln($statement->condition);
        $this->printStatementList($statement->ifStatements, $output, $nesting);

        if (count($statement->elseStatements) > 0) {
            $output->writeln('[else]');
            $this->printStatementList($statement->elseStatements, $output, $nesting);
        }

        $output->writeln('[global]');
    }
}