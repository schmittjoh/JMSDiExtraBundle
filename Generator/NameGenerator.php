<?php

namespace JMS\DiExtraBundle\Generator;

class NameGenerator
{
    private $count = 0;
    private $firstChars = 'abcdefghijklmnopqrstuvwxyz';
    private $firstCharsLength = 26;
    private $nonFirstChars = 'abcdefghijklmnopqrstuvwxyz0123456789_';
    private $nonFirstCharsLength = 37;
    private $reservedNames = array();

    public function addReservedName($name)
    {
        $this->reservedNames[$name] = true;
    }

    public function setFirstChars($chars)
    {
        $this->firstChars = $chars;
        $this->firstCharsLength = strlen($chars);
    }

    public function setNonFirstChars($chars)
    {
        $this->nonFirstChars = $chars;
        $this->nonFirstCharsLength = strlen($chars);
    }

    public function reset()
    {
        $this->count = 0;
    }

    public function nextName()
    {
        while (true) {
            $name = '';
            $i = $this->count;

            if ('' === $name) {
                $name .= $this->firstChars[$i%$this->firstCharsLength];
                $i = intval($i/$this->firstCharsLength);
            }

            while ($i > 0) {
                $i -= 1;
                $name .= $this->nonFirstChars[$i%$this->nonFirstCharsLength];
                $i = intval($i/$this->nonFirstCharsLength);
            }

            $this->count += 1;

            // check that the name is not reserved
            if (isset($this->reservedNames[$name])) {
                continue;
            }

            return $name;
        }
    }
}