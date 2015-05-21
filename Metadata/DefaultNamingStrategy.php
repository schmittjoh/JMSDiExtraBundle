<?php

namespace JMS\DiExtraBundle\Metadata;

class DefaultNamingStrategy implements NamingStrategy
{
    /**
     * Returns a service name for an annotated class.
     *
     * @param string $name The fully-qualified class name.
     *
     * @return string A service name.
     */
    public function classToServiceName($name)
    {
        $name = preg_replace('/(?<=[a-zA-Z0-9])[A-Z]/', '_\\0', $name);

        return strtolower(strtr($name, '\\', '.'));
    }
}
