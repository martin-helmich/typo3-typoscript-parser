<?php
namespace Helmich\TypoScriptParser\Tokenizer;

class Token implements TokenInterface
{
    /** @var string */
    private $type;

    /** @var string */
    private $value;

    /** @var int */
    private $line;

    /** @var array */
    private $patternMatches;

    /**
     * @param string $type
     * @param string $value
     * @param int    $line
     * @param array  $patternMatches
     */
    public function __construct($type, $value, $line, array $patternMatches = [])
    {
        $this->type           = $type;
        $this->value          = $value;
        $this->line           = $line;
        $this->patternMatches = $patternMatches;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $string
     * @return array
     */
    public function getSubMatch($string)
    {
        return isset($this->patternMatches[$string]) ? $this->patternMatches[$string] : null;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }
}
