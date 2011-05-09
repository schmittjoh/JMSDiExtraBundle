<?php

namespace JMS\DiExtraBundle\Exception;

class InvalidTypeException extends InvalidArgumentException
{
    public function __construct($annotName, $attrName, $expected, $actual)
    {
        $msg = sprintf('The attribute "%s" on annotation "@%s" is expected to be of type %s, but got %s.', $attrName, $annotName, $expected, gettype($actual));

        parent::__construct($msg);
    }
}