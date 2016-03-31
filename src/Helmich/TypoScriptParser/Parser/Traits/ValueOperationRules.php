<?php
namespace Helmich\TypoScriptParser\Parser\Traits;

use Helmich\TypoScriptParser\Parser\AST\ObjectPath;
use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Operator\Copy;
use Helmich\TypoScriptParser\Parser\AST\Operator\Delete;
use Helmich\TypoScriptParser\Parser\AST\Operator\Modification;
use Helmich\TypoScriptParser\Parser\AST\Operator\ModificationCall;
use Helmich\TypoScriptParser\Parser\AST\Operator\ObjectCreation;
use Helmich\TypoScriptParser\Parser\AST\Operator\Reference;
use Helmich\TypoScriptParser\Parser\AST\Scalar;
use Helmich\TypoScriptParser\Parser\ParseError;
use Helmich\TypoScriptParser\Parser\ParserContext;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;

trait ValueOperationRules
{

    private function parseValueOperation(ParserContext $context)
    {
        if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_ASSIGNMENT) {
            $this->parseAssignment($context);
        } else if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_COPY || $context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_REFERENCE) {
            $this->parseCopyOrReference($context);
        } else if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_MODIFY) {
            $this->parseModification($context);
        } else if ($context->token(1)->getType() === TokenInterface::TYPE_OPERATOR_DELETE) {
            $this->parseDeletion($context);
        } else if ($context->token(1)->getType() === TokenInterface::TYPE_RIGHTVALUE_MULTILINE) {
            $this->parseMultilineAssigment($context);
        }
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

        $target = $c->context()->parent()->append($targetToken->getValue());
        $cls    = ($c->token(1)->getType() === TokenInterface::TYPE_OPERATOR_COPY) ? Copy::class : Reference::class;

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
        if ($context->token(2)->getType() !== TokenInterface::TYPE_WHITESPACE) {
            throw new ParseError(
                'Unexpected token ' . $context->token(2)->getType() . ' after delete operator (expected line break).',
                1403011201,
                $context->token()->getLine()
            );
        }

        $context->statements()[] = new Delete($context->context(), $context->token(1)->getLine());
        $context->next(1);
    }

    /**
     * @param ParserContext $context
     */
    private function parseMultilineAssigment(ParserContext $context)
    {
        $context->statements()[] = new Assignment(
            $context->context(),
            new Scalar($context->token(1)->getValue()),
            $context->token(1)->getLine()
        );
        $context->next();
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
}