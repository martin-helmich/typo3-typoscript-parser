<?php

declare(strict_types=1);

namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Preprocessor that combines multiple preprocessors
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class ProcessorChain implements Preprocessor
{
    /** @var Preprocessor[] */
    protected array $processors = [];

    public function with(Preprocessor $next): self
    {
        $new             = new self();
        $new->processors = array_merge($this->processors, [$next]);
        return $new;
    }

    /**
     * @param string $contents Un-processed Typoscript contents
     * @return string Processed TypoScript contents
     */
    public function preprocess(string $contents): string
    {
        foreach ($this->processors as $p) {
            $contents = $p->preprocess($contents);
        }

        return $contents;
    }
}
