<?php

namespace JMS\DiExtraBundle\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class FormType
{
    /** @var string */
    public $alias;

    public function __construct()
    {
        if (0 === func_num_args()) {
            return;
        }
        $values = func_get_arg(0);

        if (isset($values['value'])) {
            if (!is_string($values['value'])) {
                throw new \RuntimeException(sprintf('"value" must be a string.'));
            }
            $this->alias = $values['value'];
        }
    }
}