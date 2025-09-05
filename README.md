TypoScript Parser
=================

[![PHP type checking and unit testing](https://github.com/martin-helmich/typo3-typoscript-parser/actions/workflows/php.yml/badge.svg)](https://github.com/martin-helmich/typo3-typoscript-parser/actions/workflows/php.yml)

Author
======

Martin Helmich (typo3 at martin-helmich dot de)

Synopsis
========

This package contains a library offering a tokenizer and a parser for TYPO3's
configuration language, "TypoScript".

Why?
====

Just as [typoscript-lint](https://github.com/martin-helmich/typo3-typoscript-lint),
this project started of as a simple programming exercise.
Tokenizer and parser could probably be implemented in a better way (it's open source, go for it!).

Usage
=====

Parsing TypoScript
------------------

You can use the `Helmich\TypoScriptParser\Parser\Parser` class to generate a syntax tree from source code input.
The class requires an instance of the `Helmich\TypoScriptParser\Tokenizer\Tokenizer` class as dependency.
When using the Symfony DependencyInjection component, you can use the `parser` service for this.

```php
use Helmich\TypoScriptParser\Parser\Parser,
    Helmich\TypoScriptParser\Tokenizer\Tokenizer;

$typoscript = file_get_contents('path/to/typoscript.ts');
$parser     = new Parser(new Tokenizer());
$statements = $parser->parse($typoscript);
```

Analyzing TypoScript
--------------------

You can analyze the generated syntax tree by implementing [visitors](http://en.wikipedia.org/wiki/Visitor_pattern).
For example, let's implement a check that checks for non-CGL-compliant variable
names (there's probably no use case for that, just as a simple example):

First, we need the respective visitor implementation:

```php
use Helmich\TypoScriptParser\Parser\Traverser\Visitor,
    Helmich\TypoScriptParser\Parser\AST\Statement,
    Helmich\TypoScriptParser\Parser\AST\Operator\Assignment,
    Helmich\TypoScriptParser\Parser\AST\NestedAssignment;

class VariableNamingCheckVisitor implements Visitor {
    public function enterTree(array $statements) {}
    public function enterNode(Statement $statement) {
        if ($statement instanceof Assignment || $statement instanceof NestedAssignment) {
            if (!preg_match(',^[0-9]+$,', $statement->object->relativePath)) {
                throw new \Exception('Variable names must be numbers only!');
            }
        }
    }
    public function exitNode(Statement $statement) {}
    public function exitTree(array $statements) {}
}
```

Then traverse the syntax tree:

```php
use Helmich\TypoScriptParser\Parser\Traverser\Traverser;

$traverser = new Traverser($statements);
$traverser->addVisitor(new VariableNamingCheckVisitor());
$traverser->walk();
```

Printing TypoScript
-------------------

When you are using this package for code transformation, 
you might want to print a modified syntax tree back into a file.
You can use the `PrettyPrinter` class for this:

```php
use Helmich\TypoScriptParser\Parser\Printer\PrettyPrinter;
use Symfony\Component\Console\Output\StreamOutput;

$syntaxTree = [...];

$output = new StreamOutput(fopen('path/to/file', 'w'));

$printer = new PrettyPrinter();
$printer->printStatements($syntaxTree, $output);
```

To get more fine-grained control over the output, you can pass a configuration
object into your printer instance:

```php

$printerConfiguration = PrettyPrinterConfiguration::create()
    ->withSpaceIndentation(2)
    ->withIndentConditions()
    ->withClosingGlobalStatement()
    ->withConditionTermination(PrettyPrinterConditionTermination::EnforceEnd);

$printer = new PrettyPrinter($printerConfiguration);
$printer->printStatements($syntaxTree, $output);
```

Dumping the AST
---------------

You can dump out the AST with the following code:

```php
use Helmich\TypoScriptParser\Parser\ParseError;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Parser\StatementDumper;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;

$code = <<<'CODE'
# A comment
CODE;

$parser = new Parser(new Tokenizer());

try {
    $statements = $parser->parseString($code);
} catch (ParseError $error) {
    echo "Parse error: {$error->getMessage()}";
    exit;
}

$dumper = new StatementDumper();
echo $dumper->dump($statements);
```
