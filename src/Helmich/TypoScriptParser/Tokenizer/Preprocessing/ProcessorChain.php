<?php
namespace Helmich\TypoScriptParser\Tokenizer\Preprocessing;

/**
 * Preprocessor that combines multiple preprocessors
 *
 * @package Helmich\TypoScriptParser\Tokenizer\Preprocessing
 */
class ProcessorChain implements Preprocessor
{
    /** @var Preprocessor[] */
    private $processors = [];

    /**
     * @param Preprocessor $next
     * @return static
     */
    public function with(Preprocessor $next)
    {
        $new = new static();
        $new->processors = array_merge($this->processors, [$next]);
        return $new;
    }

    /**
     * @param string $contents Un-processed Typoscript contents
     * @return string Processed TypoScript contents
     */
    public function preprocess($contents)
    {
        foreach ($this->processors as $p) {
            $contents = $p->preprocess($contents);
        }

        return $contents;
    }
}
