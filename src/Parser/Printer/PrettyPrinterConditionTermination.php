<?php

namespace Helmich\TypoScriptParser\Parser\Printer;

/**
 * Defines the different options as to how the printer should treat the termination statements of condition blocks.
 */
enum PrettyPrinterConditionTermination
{
    /**
     * Keep condition terminations as they were in the input source code.
     */
    case Keep;

    /**
     * Use `[global]` everywhere to terminate conditions.
     */
    case EnforceGlobal;

    /**
     * Use `[end]` everywhere to terminate conditions.
     */
    case EnforceEnd;
}
