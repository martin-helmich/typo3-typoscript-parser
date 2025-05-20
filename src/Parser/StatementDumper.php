<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatementTerminator;
use Helmich\TypoScriptParser\Parser\AST\Node;
use Helmich\TypoScriptParser\Parser\AST\NopStatement;
use Helmich\TypoScriptParser\Parser\AST\Statement;

class StatementDumper
{
    private bool $dumpLineNumbers;

    /**
     * @param array{dumpLineNumbers?: bool} $options
     */
    public function __construct(array $options = [])
    {
        $this->dumpLineNumbers = $options['dumpLineNumbers'] ?? false;
    }

    /**
     * @param array<Statement> $statements
     */
    public function dump(array $statements): string
    {
        return $this->dumpRecursive($statements);
    }

    /**
     * @param Node|array<Node>|ConditionalStatementTerminator|null $statement
     */
    private function dumpRecursive(Node|array|ConditionalStatementTerminator|null $statement): string
    {
        if ($statement instanceof NopStatement) {
            return get_class($statement) . '()';
        }

        if ($statement instanceof Node) {
            $r = get_class($statement);
            if ($this->dumpLineNumbers && $lineNumber = $this->dumpLineNumber($statement)) {
                $r .= $lineNumber;
            }
            $r .= '(';

            $properties = $statement->getSubNodeNames();
            foreach ($properties as $key) {
                $r .= "\n    " . $key . ': ';

                $value = $statement->$key;
                if ($value === null) {
                    $r .= 'null';
                } elseif ($value === true) {
                    $r .= 'true';
                } elseif ($value === false) {
                    $r .= 'false';
                } elseif (is_scalar($value)) {
                    $r = rtrim($r . $value);
                } else {
                    $r .= str_replace("\n", "\n    ", $this->dumpRecursive($value));
                }
            }
        } elseif (is_array($statement)) {
            $r = 'array(';
            foreach ($statement as $key => $value) {
                $r .= "\n    " . $key . ': ';

                if ($value === null) {
                    $r .= 'null';
                } elseif ($value === true) {
                    $r .= 'true';
                } elseif ($value === false) {
                    $r .= 'false';
                } elseif (is_scalar($value)) {
                    $r = rtrim($r . $value);
                } else {
                    $r .= str_replace("\n", "\n    ", $this->dumpRecursive($value));
                }
            }
        } elseif ($statement === null) {
            return 'null';
        } elseif ($statement instanceof ConditionalStatementTerminator) {
            $r = get_class($statement);
            return $r . ':' . $statement->name;
        } else {
            throw new \InvalidArgumentException('Unknown statement type: ' . var_export($statement, true));
        }

        return $r . "\n)";
    }

    private function dumpLineNumber(Node $statement): ?string
    {
        if (!$statement instanceof Statement) {
            return null;
        }

        return '[' . $statement->sourceLine . ']';
    }
}
