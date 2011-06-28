<?php

namespace JMS\DiExtraBundle\Tests\Fixture\Validator;

use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Validator as ValidatorAnnot;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @ValidatorAnnot("foobar")
 */
class Validator extends ConstraintValidator
{
    private $foo;

    /**
     * @InjectParams
     */
    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    public function isValid($value, Constraint $constraint)
    {
        return true;
    }
}