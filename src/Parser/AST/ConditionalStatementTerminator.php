<?php

namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * Models the way a condition was terminated.
 */
enum ConditionalStatementTerminator
{
    case Global;
    case End;
    case Unterminated;
}
