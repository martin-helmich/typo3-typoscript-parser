<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatementTerminator;
use Helmich\TypoScriptParser\Parser\AST\Node;
use Helmich\TypoScriptParser\Parser\AST\NopStatement;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use InvalidArgumentException;

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

    private function dumpRecursive(mixed $statement): string
    {
        if (is_array($statement)) {
            /** @var array<int, mixed> $statement */
            return $this->handleArray($statement);
        }

        return match(true) {
            $statement instanceof NopStatement => get_class($statement) . '()',
            $statement instanceof ConditionalStatementTerminator => get_class($statement) . ':' . $statement->name,
            $statement instanceof Node => $this->handleNode($statement),
            $statement === null => 'null',
            default => throw new InvalidArgumentException('Unknown statement type: ' . var_export($statement, true)),
        };
    }

    private function handleNode(Node $statement): string
    {
        $class = get_class($statement);
        $line = $this->dumpLineNumbers && $statement instanceof Statement ? "[{$statement->sourceLine}]" : '';
        $result = "{$class}{$line}(";

        foreach ($statement->getSubNodeNames() as $key) {
            $value = $this->dumpValue($statement->$key);
            $result .= "\n    {$key}:";
            if ($value !== '') {
                $result .= " {$value}";
            }
        }

        return $result . "\n)";
    }

    /**
     * @param array<int, mixed> $statements
     */
    private function handleArray(array $statements): string
    {
        $result = 'array(';
        foreach ($statements as $key => $value) {
            $formattedValue = $this->dumpValue($value);
            $result .= "\n    {$key}:";
            if ($formattedValue !== '') {
                $result .= " {$formattedValue}";
            }
        }
        return $result . "\n)";
    }

    private function dumpValue(mixed $value): string
    {
        return match(true) {
            $value === null => 'null',
            $value === true => 'true',
            $value === false => 'false',
            is_string($value) => $value,
            is_scalar($value) => (string)$value,
            default => str_replace("\n", "\n    ", $this->dumpRecursive($value)),
        };
    }
}
