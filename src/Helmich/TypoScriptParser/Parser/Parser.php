<?php
namespace Helmich\TypoScriptParser\Parser;

use Helmich\TypoScriptParser\Parser\AST\ConditionalStatement;
use Helmich\TypoScriptParser\Parser\AST\DirectoryIncludeStatement;
use Helmich\TypoScriptParser\Parser\AST\FileIncludeStatement;
use Helmich\TypoScriptParser\Parser\AST\NestedAssignment;
use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\Copy;
use Helmich\TypoScriptParser\Parser\AST\Operator\Delete;
use Helmich\TypoScriptParser\Parser\AST\Operator\Modification;
use Helmich\TypoScriptParser\Parser\AST\Operator\ModificationCall;
use Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation;
use Helmich\TypoScriptParser\Parser\AST\Operator\Reference;
use Helmich\TypoScriptParser\Parser\AST\Scalar;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use Helmich\TypoScriptParser\Tokenizer\TokenizerInterface;

class Parser implements ParserInterface
{

    /** @var TokenizerInterface */
    private $tokenizer;

    /**
     * Parser constructor.
     *
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * Parses a stream resource.
     *
     * This can be any kind of stream supported by PHP (e.g. a filename or a URL).
     *
     * @param string $stream The stream resource.
     * @return Statement[] The syntax tree.
     */
    public function parseStream($stream)
    {
        $content = file_get_contents($stream);
        return $this->parseString($content);
    }

    /**
     * Parses a TypoScript string.
     *
     * @param string $content The string to parse.
     * @return Statement[] The syntax tree.
     */
    public function parseString($content)
    {
        $tokens = $this->tokenizer->tokenizeString($content);
        return $this->parseTokens($tokens);
    }

    /**
     * Parses a token stream.
     *
     * @param TokenInterface[] $tokens The token stream to parse.
     * @return Statement[] The syntax tree.
     */
    public function parseTokens(array $tokens)
    {
        $tokens  = $this->filterTokenStream($tokens);
        $context = new ParserContext(null, $tokens);

        for (; $context->hasNext(); $context->next()) {
            if ($context->token()->getType() === TokenInterface::TYPE_OBJECT_IDENTIFIER) {
                $objectPath = new ObjectPath($context->token()->getValue(), $context->token()->getValue());
                if ($context->token(1)->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $context->next(2);
                    $this->parseNestedStatements($context->withContext($objectPath));
                }
            }

            $this->parseToken($context);
        }

        return $context->statements()->getArrayCopy();
    }

    /**
     * @param ParserContext $context
     * @return NestedAssignment
     * @throws ParseError
     */
    private function parseToken(ParserContext $context)
    {
        if ($context->token()->getType() === TokenInterface::TYPE_OBJECT_IDENTIFIER) {
            $objectPath = new ObjectPath(
                ($context->context() ? $context->context()->absoluteName . '.' : '') . $context->token()->getValue(),
                $context->token()->getValue()
            );

            if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_ASSIGNMENT) {
                $this->parseAssignment($context->withContext($objectPath));
            } else if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_COPY || $context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_REFERENCE) {
                $this->parseCopyOrReference($context->withContext($objectPath));
            } else if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_MODIFY) {
                $this->parseModification($context->withContext($objectPath));
            } else if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_DELETE) {
                $this->parseDeletion($context->withContext($objectPath));
            } else if ($context->token(1)->getType() === TokenInterface::TYPE_RIGHTVALUE_MULTILINE) {
                $context->statements()[] = new Assignment(
                    $objectPath,
                    new Scalar($context->token(1)->getValue()),
                    $context->token(1)->getLine()
                );
                $context->next();
            }
        } else if ($context->token()->getType() === TokenInterface::TYPE_CONDITION) {
            $this->triggerParseErrorIf(
                $context->context() !== null,
                'Found condition statement inside nested assignment.',
                1403011203,
                $context->token()->getLine()
            );

            $ifStatements   = new \ArrayObject();
            $elseStatements = new \ArrayObject();

            $condition     = $context->token()->getValue();
            $conditionLine = $context->token()->getLine();

            $inElseBranch      = false;
            $currentStatements = &$ifStatements;

            $context->next();

            for (; $context->hasNext(); $context->next()) {
                if ($context->token()->getType() === TokenInterface::TYPE_CONDITION_END) {
                    $context->statements()[] = new ConditionalStatement(
                        $condition, $ifStatements->getArrayCopy(), $elseStatements->getArrayCopy(), $conditionLine
                    );
                    $context->next();
                    break;
                } elseif ($context->token()->getType() === TokenInterface::TYPE_CONDITION_ELSE) {
                    $this->triggerParseErrorIf(
                        $inElseBranch,
                        sprintf('Duplicate else in conditional statement in line %d.', $context->token()->getLine()),
                        1403011203,
                        $context->token()->getLine()
                    );

                    $inElseBranch      = true;
                    $currentStatements = &$elseStatements;
                    $context->next();
                }

                if ($context->token()->getType() === TokenInterface::TYPE_OBJECT_IDENTIFIER) {
                    $objectPath = new ObjectPath($context->token()->getValue(), $context->token()->getValue());
                    if ($context->token(1)->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                        $context->next(2);
                        $this->parseNestedStatements(
                            $context->withContext($objectPath)->withStatements($currentStatements),
                            $context->token(-2)->getLine()
                        );
                    }
                }

                $this->parseToken($context->withStatements($currentStatements));
            }
        } else if ($context->token()->getType() === TokenInterface::TYPE_INCLUDE) {
            preg_match(Tokenizer::TOKEN_INCLUDE_STATEMENT, $context->token()->getValue(), $matches);

            if ($matches['type'] === 'FILE') {
                $context->statements()[] = new FileIncludeStatement($matches['filename'], $context->token()->getLine());
            } else {
                $context->statements()[] = new DirectoryIncludeStatement(
                    $matches['filename'],
                    isset($matches['extensions']) ? $matches['extensions'] : null,
                    $context->token()->getLine()
                );
            }
        } else if ($context->token()->getType() === TokenInterface::TYPE_WHITESPACE) {
            // Pass
        } else if ($context->token()->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
            $this->triggerParseErrorIf(
                $context->context() === null,
                sprintf(
                    'Unexpected token %s when not in nested assignment in line %d.',
                    $context->token()->getType(),
                    $context->token()->getLine()
                ),
                1403011203,
                $context->token()->getLine()
            );
        } else {
            throw new ParseError(
                sprintf('Unexpected token %s in line %d.', $context->token()->getType(), $context->token()->getLine()),
                1403011202,
                $context->token()->getLine()
            );
        }
    }

    private function triggerParseErrorIf($condition, $message, $code, $line)
    {
        if ($condition) {
            throw new ParseError(
                $message,
                $code,
                $line
            );
        }
    }

    private function validateModifyOperatorRightValue(TokenInterface $token)
    {
        if ($token->getType() !== TokenInterface::TYPE_RIGHTVALUE) {
            throw new ParseError(
                'Unexpected token ' . $token->getType() . ' after modify operator.',
                1403010294,
                $token->getLine()
            );
        }

        if (!preg_match(Tokenizer::TOKEN_OBJECT_MODIFIER, $token->getValue())) {
            throw new ParseError(
                'Right side of modify operator does not look like a modifier: "' . $token->getValue() . '".',
                1403010700,
                $token->getLine()
            );
        }
    }

    private function validateCopyOperatorRightValue(TokenInterface $token)
    {
        if ($token->getType() !== TokenInterface::TYPE_RIGHTVALUE) {
            throw new ParseError(
                'Unexpected token ' . $token->getType() . ' after copy operator.',
                1403010294,
                $token->getLine()
            );
        }

        if (!preg_match(Tokenizer::TOKEN_OBJECT_REFERENCE, $token->getValue())) {
            throw new ParseError(
                'Right side of copy operator does not look like an object path: "' . $token->getValue() . '".',
                1403010699,
                $token->getLine()
            );
        }
    }

    /**
     * @param TokenInterface[] $tokens
     * @return TokenInterface[]
     */
    private function filterTokenStream($tokens)
    {
        $filteredTokens = [];
        $ignoredTokens  = [
            TokenInterface::TYPE_COMMENT_MULTILINE,
            TokenInterface::TYPE_COMMENT_ONELINE,
        ];

        $maxLine = 0;

        foreach ($tokens as $token) {
            $maxLine = max($token->getLine(), $maxLine);

            // Trim unnecessary whitespace, but leave line breaks! These are important!
            if ($token->getType() === TokenInterface::TYPE_WHITESPACE) {
                $value = trim($token->getValue(), "\t ");
                if (strlen($value) > 0) {
                    $filteredTokens[] = new Token(
                        TokenInterface::TYPE_WHITESPACE,
                        $value,
                        $token->getLine()
                    );
                }
            } elseif (!in_array($token->getType(), $ignoredTokens)) {
                $filteredTokens[] = $token;
            }
        }

        // Add two linebreak tokens; during parsing, we usually do not look more than two
        // tokens ahead; this hack ensures that there will always be at least two more tokens
        // present and we do not have to check whether these tokens exists.
        $filteredTokens[] = new Token(TokenInterface::TYPE_WHITESPACE, "\n", $maxLine + 1);
        $filteredTokens[] = new Token(TokenInterface::TYPE_WHITESPACE, "\n", $maxLine + 2);

        return $filteredTokens;
    }

    /**
     * @param ParserContext $context
     * @param int           $startLine
     * @return NestedAssignment
     * @throws ParseError
     */
    private function parseNestedStatements(ParserContext $context, $startLine = NULL)
    {
        $startLine  = $startLine ?: $context->token()->getLine();
        $statements = new \ArrayObject();
        $subContext = $context->withStatements($statements);

        for (; $context->hasNext(); $context->next()) {
            if ($context->token()->getType() === TokenInterface::TYPE_OBJECT_IDENTIFIER) {
                $objectPath = new ObjectPath(
                    $context->context()->absoluteName . '.' . $context->token()->getValue(),
                    $context->token()->getValue()
                );

                if ($context->token(1)->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $context->next(2);
                    $this->parseNestedStatements(
                        $context->withContext($objectPath)->withStatements($statements)
                    );
                    continue;
                }
            }

            $this->parseToken($subContext);

            if ($context->token()->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
                $context->statements()[] = new NestedAssignment($context->context(), $statements->getArrayCopy(), $startLine);
                $context->next();
                return;
            }
        }

        throw new ParseError('Unterminated nested statement!');
    }

    /**
     * @param ParserContext $context
     */
    private function parseAssignment(ParserContext $context)
    {
        switch ($context->token(2)->getType()) {
            case TokenInterface::TYPE_OBJECT_CONSTRUCTOR:
                $context->statements()[] = new ObjectCreation(
                    $context->context(),
                    new Scalar($context->token(2)->getValue()),
                    $context->token(2)->getLine()
                );
                $context->next(2);
                break;
            case TokenInterface::TYPE_RIGHTVALUE:
                $context->statements()[] = new Assignment(
                    $context->context(),
                    new Scalar($context->token(2)->getValue()),
                    $context->token(2)->getLine()
                );
                $context->next(2);
                break;
            case TokenInterface::TYPE_WHITESPACE:
                $context->statements()[] = new Assignment($context->context(), new Scalar(''), $context->token()->getLine());
                $context->next();
                break;
        }
    }

    /**
     * @param ParserContext $c
     * @throws ParseError
     */
    private function parseCopyOrReference(ParserContext $c)
    {
        $targetToken = $c->token(2);
        $this->validateCopyOperatorRightValue($targetToken);

        $target = new ObjectPath($targetToken->getValue(), $targetToken->getValue());
        if ($targetToken->getValue()[0] === '.') {
            $target = $c->context()->parent()->append($targetToken->getValue());
        }

        $cls = ($c->token(1)->getType() === TokenInterface::TYPE_OPERATOR_COPY) ? Copy::class : Reference::class;

        $c->statements()[] = new $cls($c->context(), $target, $c->token(1)->getLine());
        $c->next(2);
    }

    /**
     * @param ParserContext $context
     * @throws ParseError
     */
    private function parseModification(ParserContext $context)
    {
        $this->validateModifyOperatorRightValue($context->token(2));

        preg_match(Tokenizer::TOKEN_OBJECT_MODIFIER, $context->token(2)->getValue(), $matches);

        $call                    = new ModificationCall($matches['name'], $matches['arguments']);
        $context->statements()[] = new Modification($context->context(), $call, $context->token(2)->getLine());

        $context->next(2);
    }

    /**
     * @param ParserContext $context
     * @throws ParseError
     */
    private function parseDeletion(ParserContext $context)
    {
        $this->triggerParseErrorIf(
            $context->token(2)->getType() !== TokenInterface::TYPE_WHITESPACE,
            'Unexpected token ' . $context->token(2)->getType() . ' after delete operator (expected line break).',
            1403011201,
            $context->token()->getLine()
        );

        $context->statements()[] = new Delete($context->context(), $context->token(1)->getLine());
        $context->next(1);
    }

}