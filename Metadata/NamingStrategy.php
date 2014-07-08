<?php

namespace JMS\DiExtraBundle\Metadata;

interface NamingStrategy
{
    /**
     * Returns a service name for an annotated class.
     *
     * @param string $className The fully-qualified class name.
     *
     * @return string A service name.
     */
    public function classToServiceName($className);
}
