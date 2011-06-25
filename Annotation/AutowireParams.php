<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

final class AutowireParams
{
    public $params = array();

    public function __construct(array $values)
    {
        if (isset($values['params'])) {
            $values['value'] = $values['params'];
        }

        if (isset($values['value'])) {
            if (!is_array($values['value'])) {
                throw new InvalidTypeException('AutowireParams', 'value', 'array', $values['value']);
            }

            foreach ($values['value'] as $k => $v) {
                if (!$v instanceof Autowire) {
                    throw new InvalidTypeException('AutowireParams', sprintf('value[%s]', $k), '@Autowire', $v);
                }

                $this->params[$k] = $v;
            }
        }
    }
}