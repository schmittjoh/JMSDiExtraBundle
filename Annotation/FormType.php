<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

/**
 * @Annotation
 * @Target("CLASS")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
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
            $values['alias'] = $values['value'];
        }

        if (isset($values['alias'])) {
            if (!is_string($values['alias'])) {
                throw new InvalidTypeException('FormType', 'alias', 'string', $values['alias']);
            }

            $this->alias = $values['alias'];
        }
    }
}