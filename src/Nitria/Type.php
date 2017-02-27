<?php

declare(strict_types = 1);

namespace Nitria;

class Type
{
    const SCALAR_TYPE_LIST = [
        "bool",
        "int",
        "float",
        "string",
        "array",
        "callable"
    ];

    /**
     * @var string
     */
    protected $scalarName;

    /**
     * @var ClassName
     */
    protected $className;

    /**
     * @var bool
     */
    protected $isArray;

    /**
     * Type constructor.
     *
     * @param string $type
     */
    public function __construct(string $type = null)
    {
        if ($type === null) {
            return;
        }

        $type = trim($type);
        $this->isArray = StringUtil::endsWith($type, '[]');

        $type = trim($type, '[]');

        if (in_array($type, self::SCALAR_TYPE_LIST)) {
            $this->scalarName = $type;
        } else {
            $this->className = new ClassName($type);
        }
    }

    /**
     * @return bool
     */
    public function needsUseStatement() : bool
    {
        return $this->className !== null && $this->className->getNamespaceName() !== null;
    }

    /**
     * @return null|string
     */
    public function getUseStatement()
    {
        if (!$this->needsUseStatement()) {
            return null;
        }
        return $this->className->getClassName();
    }

    /**
     * @return string
     */
    public function getCodeType()
    {
        if ($this->isArray) {
            return 'array';
        }
        if ($this->className !== null) {
            return $this->className->getClassShortName();
        }

        if ($this->scalarName === null) {
            return null;
        }

        return $this->scalarName;
    }

    /**
     * @return string
     */
    public function getDocBlockType() : string
    {
        if ($this->scalarName === null && $this->className === null) {
            return 'mixed';
        }
        $type = ($this->className !== null) ? $this->className->getClassShortName() : $this->scalarName;
        $value = ($this->isArray) ? $type . '[]' : $type;
        return $value;
    }

    /**
     * @return ClassName
     */
    public function getClassName()
    {
        return $this->className;
    }
}