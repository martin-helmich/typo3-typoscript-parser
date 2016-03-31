<?php
namespace Helmich\TypoScriptParser\Parser;

use Helmich\TypoScriptParser\Parser\AST\Builder;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Helmich\TypoScriptParser\Tokenizer\Token;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use Helmich\TypoScriptParser\Tokenizer\TokenizerInterface;

/**
 * Class Parser
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser
 */
class Parser implements ParserInterface
{
    /** @var TokenizerInterface */
    private $tokenizer;

    /** @var Builder */
    private $builder;

    /**
     * Parser constructor.
     *
     * @param TokenizerInterface $tokenizer
     * @param Builder            $astBuilder
     */
    public function __construct(TokenizerInterface $tokenizer, Builder $astBuilder = null)
    {
        $this->tokenizer = $tokenizer;
        $this->builder   = $astBuilder ?: new Builder();
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
        $tokens = $this->filterTokenStream($tokens);
        $state  = new ParserState(null, $tokens);

        for (; $state->hasNext(); $state->next()) {
            if ($state->token()->getType() === TokenInterface::TYPE_OBJECT_IDENTIFIER) {
                $objectPath = $this->builder->path($state->token()->getValue(), $state->token()->getValue());
                if ($state->token(1)->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $state->next(2);
                    $this->parseNestedStatements($state->withContext($objectPath));
                }
            }

            $this->parseToken($state);
        }

        return $state->statements()->getArrayCopy();
    }

    /**
     * @param ParserState $state
     * @return void
     * @throws ParseError
     */
    private function parseToken(ParserState $state)
    {
        switch ($state->token()->getType()) {
            case TokenInterface::TYPE_OBJECT_IDENTIFIER:
                $objectPath = $state->context()->append($state->token()->getValue());
                $this->parseValueOperation($state->withContext($objectPath));
                break;
            case TokenInterface::TYPE_CONDITION:
                $this->parseCondition($state);
                break;
            case TokenInterface::TYPE_INCLUDE:
                $this->parseInclude($state);
                break;
            case TokenInterface::TYPE_WHITESPACE:
                break;
            case TokenInterface::TYPE_BRACE_CLOSE:
                $this->triggerParseErrorIf(
                    $state->context()->depth() === 0,
                    sprintf(
                        'Unexpected token %s when not in nested assignment in line %d.',
                        $state->token()->getType(),
                        $state->token()->getLine()
                    ),
                    1403011203,
                    $state->token()->getLine()
                );
                break;
            default:
                throw new ParseError(
                    sprintf('Unexpected token %s in line %d.', $state->token()->getType(), $state->token()->getLine()),
                    1403011202,
                    $state->token()->getLine()
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
     * @param ParserState $state
     * @param int         $startLine
     * @return void
     * @throws ParseError
     */
    private function parseNestedStatements(ParserState $state, $startLine = null)
    {
        $startLine  = $startLine ?: $state->token()->getLine();
        $statements = new \ArrayObject();
        $subContext = $state->withStatements($statements);

        for (; $state->hasNext(); $state->next()) {
            if ($state->token()->getType() === TokenInterface::TYPE_OBJECT_IDENTIFIER) {
                $objectPath = $this->builder->path(
                    $state->context()->absoluteName . '.' . $state->token()->getValue(),
                    $state->token()->getValue()
                );

                if ($state->token(1)->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $state->next(2);
                    $this->parseNestedStatements(
                        $state->withContext($objectPath)->withStatements($statements)
                    );
                    continue;
                }
            }

            $this->parseToken($subContext);

            if ($state->token()->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
                $state->statements()[] = $this->builder->nested(
                    $state->context(),
                    $statements->getArrayCopy(),
                    $startLine
                );
                $state->next();
                return;
            }
        }

        throw new ParseError('Unterminated nested statement!');
    }

    /**
     * @param ParserState $state
     * @throws ParseError
     */
    private function parseCondition(ParserState $state)
    {
        $this->triggerParseErrorIf(
            $state->context()->depth() !== 0,
            'Found condition statement inside nested assignment.',
            1403011203,
            $state->token()->getLine()
        );

        $ifStatements   = new \ArrayObject();
        $elseStatements = new \ArrayObject();

        $condition     = $state->token()->getValue();
        $conditionLine = $state->token()->getLine();

        $inElseBranch = false;
        $subContext   = $state->withStatements($ifStatements);

        $state->next();

        for (; $state->hasNext(); $state->next()) {
            if ($state->token()->getType() === TokenInterface::TYPE_CONDITION_END) {
                $state->statements()[] = $this->builder->condition(
                    $condition,
                    $ifStatements->getArrayCopy(),
                    $elseStatements->getArrayCopy(),
                    $conditionLine
                );
                $state->next();
                break;
            } elseif ($state->token()->getType() === TokenInterface::TYPE_CONDITION_ELSE) {
                $this->triggerParseErrorIf(
                    $inElseBranch,
                    sprintf('Duplicate else in conditional statement in line %d.', $state->token()->getLine()),
                    1403011203,
                    $state->token()->getLine()
                );

                $inElseBranch = true;
                $subContext   = $subContext->withStatements($elseStatements);
                $state->next();
            }

            if ($state->token()->getType() === TokenInterface::TYPE_OBJECT_IDENTIFIER) {
                $objectPath = $this->builder->path($state->token()->getValue(), $state->token()->getValue());
                if ($state->token(1)->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $state->next(2);
                    $this->parseNestedStatements(
                        $subContext->withContext($objectPath),
                        $subContext->token(-2)->getLine()
                    );
                }
            }

            $this->parseToken($subContext);
        }
    }

    /**
     * @param ParserState $state
     */
    private function parseInclude(ParserState $state)
    {
        preg_match(Tokenizer::TOKEN_INCLUDE_STATEMENT, $state->token()->getValue(), $matches);

        if ($matches['type'] === 'FILE') {
            $state->statements()[] = $this->builder->includeFile($matches['filename'], $state->token()->getLine());
            return;
        }

        $state->statements()[] = $this->builder->includeDirectory(
            $matches['filename'],
            isset($matches['extensions']) ? $matches['extensions'] : null,
            $state->token()->getLine()
        );
    }

    /**
     * @param ParserState $state
     * @throws ParseError
     */
    private function parseValueOperation(ParserState $state)
    {
        switch ($state->token(1)->getType()) {
            case TokenInterface::TYPE_OPERATOR_ASSIGNMENT:
                $this->parseAssignment($state);
                break;
            case TokenInterface::TYPE_OPERATOR_COPY:
            case TokenInterface::TYPE_OPERATOR_REFERENCE:
                $this->parseCopyOrReference($state);
                break;
            case TokenInterface::TYPE_OPERATOR_MODIFY:
                $this->parseModification($state);
                break;
            case TokenInterface::TYPE_OPERATOR_DELETE:
                $this->parseDeletion($state);
                break;
            case TokenInterface::TYPE_RIGHTVALUE_MULTILINE:
                $this->parseMultilineAssigment($state);
                break;
        }
    }

    /**
     * @param ParserState $state
     */
    private function parseAssignment(ParserState $state)
    {
        switch ($state->token(2)->getType()) {
            case TokenInterface::TYPE_OBJECT_CONSTRUCTOR:
                $state->statements()[] = $this->builder->op()->objectCreation(
                    $state->context(),
                    $this->builder->scalar($state->token(2)->getValue()),
                    $state->token(2)->getLine()
                );
                $state->next(2);
                break;
            case TokenInterface::TYPE_RIGHTVALUE:
                $state->statements()[] = $this->builder->op()->assignment(
                    $state->context(),
                    $this->builder->scalar($state->token(2)->getValue()),
                    $state->token(2)->getLine()
                );
                $state->next(2);
                break;
            case TokenInterface::TYPE_WHITESPACE:
                $state->statements()[] = $this->builder->op()->assignment(
                    $state->context(),
                    $this->builder->scalar(''),
                    $state->token()->getLine()
                );
                $state->next();
                break;
        }
    }

    /**
     * @param ParserState $state
     * @throws ParseError
     */
    private function parseCopyOrReference(ParserState $state)
    {
        $targetToken = $state->token(2);
        $this->validateCopyOperatorRightValue($targetToken);

        $target = $state->context()->parent()->append($targetToken->getValue());
        $type   = ($state->token(1)->getType() === TokenInterface::TYPE_OPERATOR_COPY) ? 'copy' : 'reference';

        $state->statements()[] = $this->builder->op()->{$type}($state->context(), $target, $state->token(1)->getLine());
        $state->next(2);
    }

    /**
     * @param ParserState $state
     * @throws ParseError
     */
    private function parseModification(ParserState $state)
    {
        $this->validateModifyOperatorRightValue($state->token(2));

        preg_match(Tokenizer::TOKEN_OBJECT_MODIFIER, $state->token(2)->getValue(), $matches);

        $call                  = $this->builder->op()->modificationCall($matches['name'], $matches['arguments']);
        $state->statements()[] = $this->builder->op()->modification($state->context(), $call, $state->token(2)->getLine());

        $state->next(2);
    }

    /**
     * @param ParserState $state
     * @throws ParseError
     */
    private function parseDeletion(ParserState $state)
    {
        if ($state->token(2)->getType() !== TokenInterface::TYPE_WHITESPACE) {
            throw new ParseError(
                'Unexpected token ' . $state->token(2)->getType() . ' after delete operator (expected line break).',
                1403011201,
                $state->token()->getLine()
            );
        }

        $state->statements()[] = $this->builder->op()->delete($state->context(), $state->token(1)->getLine());
        $state->next(1);
    }

    /**
     * @param ParserState $state
     */
    private function parseMultilineAssigment(ParserState $state)
    {
        $state->statements()[] = $this->builder->op()->assignment(
            $state->context(),
            $this->builder->scalar($state->token(1)->getValue()),
            $state->token(1)->getLine()
        );
        $state->next();
    }

    /**
     * @param TokenInterface $token
     * @throws ParseError
     */
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

    /**
     * @param TokenInterface $token
     * @throws ParseError
     */
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

}