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

    /** @var int */
    private $column;

    /** @var array */
    private $patternMatches;

    /**
     * @param string $type
     * @param string $value
     * @param int    $line
     * @param int    $column
     * @param array  $patternMatches
     */
    public function __construct($type, $value, $line, $column, array $patternMatches = [])
    {
        $this->type           = $type;
        $this->value          = $value;
        $this->line           = $line;
        $this->column         = $column;
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

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }
}
