<?php

namespace App\Http\Objects;

class BaseObject
{
    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set(string $name, $value): self
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->$name);
    }

    /**
     * @param string $name
     * @return BaseObject
     */
    public function __unset(string $name): self
    {
        unset($this->$name);
    }
}
