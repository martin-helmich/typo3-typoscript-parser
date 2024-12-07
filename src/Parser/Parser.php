<?php declare(strict_types=1);

namespace Helmich\TypoScriptParser\Parser;

use ArrayObject;
use Helmich\TypoScriptParser\Parser\AST\Builder;
use Helmich\TypoScriptParser\Parser\AST\ConditionalStatementTerminator;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Helmich\TypoScriptParser\Tokenizer\TokenizerInterface;

/**
 * Class Parser
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser
 */
class Parser implements ParserInterface
{
    private TokenizerInterface $tokenizer;

    private Builder $builder;

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
     * @throws ParseError
     */
    public function parseStream(string $stream): array
    {
        $content = file_get_contents($stream);
        if ($content === false) {
            throw new \InvalidArgumentException("could not open file '$stream'");
        }

        return $this->parseString($content);
    }

    /**
     * Parses a TypoScript string.
     *
     * @param string $string The string to parse.
     * @return Statement[] The syntax tree.
     * @throws ParseError
     */
    public function parseString(string $string): array
    {
        $tokens = $this->tokenizer->tokenizeString($string);
        return $this->parseTokens($tokens);
    }

    /**
     * Parses a token stream.
     *
     * @param TokenInterface[] $tokens The token stream to parse.
     * @return Statement[] The syntax tree.
     * @throws ParseError
     */
    public function parseTokens(array $tokens): array
    {
        $stream = (new TokenStream($tokens))->normalized();
        $state  = new ParserState($stream);

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
     * @throws ParseError
     */
    private function parseToken(ParserState $state): void
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
            case TokenInterface::TYPE_INCLUDE_NEW:
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
            case TokenInterface::TYPE_COMMENT_ONELINE:
                $state->statements()->append($this->builder->comment($state->token()->getValue(), $state->token()->getLine()));
                break;
            case TokenInterface::TYPE_COMMENT_MULTILINE:
                $state->statements()->append($this->builder->multilineComment($state->token()->getValue(), $state->token()->getLine()));
                break;
            case TokenInterface::TYPE_EMPTY_LINE:
                $state->statements()->append($this->builder->nop($state->token()->getLine()));
                break;
            default:
                throw new ParseError(
                    sprintf('Unexpected token %s in line %d.', $state->token()->getType(), $state->token()->getLine()),
                    1403011202,
                    $state->token()->getLine()
                );
        }
    }

    private function triggerParseErrorIf(bool $condition, string $message, int $code, int $line): void
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
     * @throws ParseError
     */
    private function parseNestedStatements(ParserState $state, ?int $startLine = null): void
    {
        $startLine  = $startLine ?? $state->token()->getLine();
        $statements = new ArrayObject();
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
                $state->statements()->append($this->builder->nested(
                    $state->context(),
                    $statements->getArrayCopy(),
                    $startLine
                ));
                $state->next();
                return;
            }
        }

        throw new ParseError('Unterminated nested statement!');
    }

    /**
     * @throws ParseError
     */
    private function parseCondition(ParserState $state): void
    {
        if ($state->context()->depth() !== 0) {
            throw new ParseError(
                'Found condition statement inside nested assignment.',
                1403011203,
                $state->token()->getLine()
            );
        }

        $ifStatements   = new ArrayObject();
        $elseStatements = new ArrayObject();

        $condition     = $state->token()->getValue();
        $conditionLine = $state->token()->getLine();

        $inElseBranch = false;
        $conditionEnded = false;
        $subContext   = $state->withStatements($ifStatements);

        $state->next();

        for (; $state->hasNext(); $state->next()) {
            if ($state->token()->getType() === TokenInterface::TYPE_CONDITION_END) {
                $state->statements()->append($this->builder->condition(
                    $condition,
                    $ifStatements->getArrayCopy(),
                    $elseStatements->getArrayCopy(),
                    $conditionLine
                ));
                $state->next();
                $conditionEnded = true;
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
            } elseif ($state->token()->getType() === TokenInterface::TYPE_CONDITION) {
                $state->statements()->append(
                    $this->builder->condition(
                        $condition,
                        $ifStatements->getArrayCopy(),
                        $elseStatements->getArrayCopy(),
                        $conditionLine
                    )
                );
                $conditionEnded = true;
                $this->parseCondition($state);
                break;
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

        if (!$conditionEnded) {
            $state->statements()->append($this->builder->condition(
                $condition,
                $ifStatements->getArrayCopy(),
                $elseStatements->getArrayCopy(),
                $conditionLine,
                terminator: ConditionalStatementTerminator::Unterminated,
            ));
            $state->next();
        }
    }

    private function parseInclude(ParserState $state): void
    {
        $token = $state->token();
        $extensions = null;
        $condition  = null;
        $filename = $token->getSubMatch('filename') ?? '';
        $optional = $token->getSubMatch('optional');

        if ($optional !== null) {
            list($extensions, $condition) = $this->parseIncludeOptionals($optional, $token);
        }

        if ($token->getType() === TokenInterface::TYPE_INCLUDE_NEW || $token->getSubMatch('type') === 'FILE') {
            $node = $this->builder->includeFile(
                $filename,
                $token->getType() === TokenInterface::TYPE_INCLUDE_NEW,
                $condition,
                $token->getLine()
            );
        } else {
            $node = $this->builder->includeDirectory(
                $filename,
                $extensions,
                $condition,
                $token->getLine()
            );
        }

        $state->statements()->append($node);
    }

    private function parseIncludeOptionals(string $optional, TokenInterface $token): array
    {
        if (!(preg_match_all('/((?<key>[a-z]+)="(?<value>[^"]*)\s*)+"/', $optional, $matches) > 0)) {
            return [null, null];
        }

        $extensions = null;
        $condition  = null;

        for ($i = 0; $i < count($matches[0]); $i++) {
            $key   = $matches['key'][$i];
            $value = $matches['value'][$i];

            switch ($key) {
                case "extensions":
                    if ($token->getSubMatch('type') === 'FILE') {
                        throw new ParseError("FILE includes may not have an 'extension' attribute", 0, $token->getLine());
                    }

                    $extensions = $value;
                    break;
                case "condition":
                    $condition = $value;
                    break;
                default:
                    throw new ParseError("unknown attribute '$key' found in INCLUDE statement", 0, $token->getLine());
            }
        }

        return [$extensions, $condition];
    }

    private function parseValueOperation(ParserState $state): void
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

    private function parseAssignment(ParserState $state): void
    {
        switch ($state->token(2)->getType()) {
            case TokenInterface::TYPE_OBJECT_CONSTRUCTOR:
                $state->statements()->append($this->builder->op()->objectCreation(
                    $state->context(),
                    $this->builder->scalar($state->token(2)->getValue()),
                    $state->token(2)->getLine()
                ));
                $state->next(2);
                break;
            case TokenInterface::TYPE_RIGHTVALUE:
                $state->statements()->append($this->builder->op()->assignment(
                    $state->context(),
                    $this->builder->scalar($state->token(2)->getValue()),
                    $state->token(2)->getLine()
                ));
                $state->next(2);
                break;
            case TokenInterface::TYPE_WHITESPACE:
            case TokenInterface::TYPE_EMPTY_LINE:
                $state->statements()->append($this->builder->op()->assignment(
                    $state->context(),
                    $this->builder->scalar(''),
                    $state->token()->getLine()
                ));
                $state->next();
                break;
        }
    }

    private function parseCopyOrReference(ParserState $state): void
    {
        $targetToken = $state->token(2);
        $this->validateCopyOperatorRightValue($targetToken);

        $target = $state->context()->parent()->append($targetToken->getValue());
        $type   = ($state->token(1)->getType() === TokenInterface::TYPE_OPERATOR_COPY) ? 'copy' : 'reference';
        $node   = $this->builder->op()->{$type}(
            $state->context(),
            $target,
            $state->token(1)->getLine()
        );

        $state->statements()->append($node);
        $state->next(2);
    }

    /**
     * @throws ParseError
     */
    private function parseModification(ParserState $state): void
    {
        $token = $state->token(2);
        $this->validateModifyOperatorRightValue($token);

        $name = $token->getSubMatch('name');
        $arguments = $token->getSubMatch('arguments');

        if ($name === null || $arguments === null) {
            throw new ParseError(
                'Invalid modification call; name or arguments are null',
                1403011201,
                $token->getLine()
            );
        }

        $call = $this->builder->op()->modificationCall($name, $arguments);

        $modification = $this->builder->op()->modification(
            $state->context(),
            $call,
            $token->getLine()
        );

        $state->statements()->append($modification);
        $state->next(2);
    }

    /**
     * @throws ParseError
     */
    private function parseDeletion(ParserState $state): void
    {
        $allowedTypesInDeletion = [TokenInterface::TYPE_WHITESPACE, TokenInterface::TYPE_EMPTY_LINE, TokenInterface::TYPE_COMMENT_ONELINE];

        if (!in_array($state->token(2)->getType(), $allowedTypesInDeletion, true)) {
            throw new ParseError(
                'Unexpected token ' . $state->token(2)->getType() . ' after delete operator (expected line break).',
                1403011201,
                $state->token()->getLine()
            );
        }

        $state->statements()->append($this->builder->op()->delete($state->context(), $state->token(1)->getLine()));
        $state->next();
    }

    private function parseMultilineAssigment(ParserState $state): void
    {
        $state->statements()->append($this->builder->op()->assignment(
            $state->context(),
            $this->builder->scalar($state->token(1)->getValue()),
            $state->token(1)->getLine()
        ));
        $state->next();
    }

    /**
     * @throws ParseError
     */
    private function validateModifyOperatorRightValue(TokenInterface $token): void
    {
        if ($token->getType() !== TokenInterface::TYPE_OBJECT_MODIFIER) {
            throw new ParseError(
                'Unexpected token ' . $token->getType() . ' after modify operator.',
                1403010294,
                $token->getLine()
            );
        }
    }

    /**
     * @throws ParseError
     */
    private function validateCopyOperatorRightValue(TokenInterface $token): void
    {
        if ($token->getType() !== TokenInterface::TYPE_OBJECT_IDENTIFIER) {
            throw new ParseError(
                'Unexpected token ' . $token->getType() . ' after copy operator.',
                1403010294,
                $token->getLine()
            );
        }
    }
}
