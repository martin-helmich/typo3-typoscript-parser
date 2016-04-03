<?php
namespace Helmich\TypoScriptParser\Parser\AST;

/**
 * An object path.
 *
 * @package    Helmich\TypoScriptParser
 * @subpackage Parser\AST
 */
class ObjectPath
{

    /**
     * The relative object path, as specified in the source code.
     *
     * @var string
     */
    public $relativeName;

    /**
     * The absolute object path, as evaluated from parent nested statements.
     *
     * @var
     */
    public $absoluteName;

    /**
     * Constructs a new object path.
     *
     * @param string $absoluteName The absolute object path.
     * @param string $relativeName The relative object path.
     */
    public function __construct($absoluteName, $relativeName)
    {
        $this->absoluteName = $absoluteName;
        $this->relativeName = $relativeName;
    }

    /**
     * @return int
     */
    public function depth()
    {
        return count(explode('.', $this->absoluteName));
    }

    /**
     * Builds the path to the parent object.
     *
     * @return ObjectPath The path to the parent object.
     */
    public function parent()
    {
        $components = explode('.', $this->absoluteName);
        if (count($components) === 1) {
            return new RootObjectPath();
        }
        array_pop($components);
        return new static(implode('.', $components), $components[count($components) - 1]);
    }

    /**
     * @param string $name
     * @return static
     */
    public function append($name)
    {
        if ($name[0] === '.') {
            return new static($this->absoluteName . $name, $name);
        }
        return new static($this->absoluteName . '.' . $name, $name);
    }
}