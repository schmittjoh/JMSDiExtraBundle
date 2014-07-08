<?php

namespace JMS\DiExtraBundle\Metadata;

class DefaultNamingStrategy implements NamingStrategy
{
    /**
     * List of namespace / class name components to be stripped from the final service name.
     * The structure should be: array('component' => ['prefix', 'namespace', 'suffix']).
     *
     * @var  array
     */
    private $namespaceStrip;

    /**
     * Should we add underscores when de-camelcasing?
     *
     * @var bool
     */
    private $underscoreify;


    /**
     * @param array $namespaceStrip
     * @param bool  $underscoreify
     */
    public function __construct(array $namespaceStrip = array(), $underscoreify = true)
    {
        $this->namespaceStrip = $namespaceStrip;
        $this->underscoreify = $underscoreify;
    }


    /**
     * Returns a service name for an annotated class.
     *
     * @param string $className The fully-qualified class name.
     *
     * @return string A service name.
     */
    public function classToServiceName($className)
    {
        $name = $className;

        if (!empty($this->namespaceStrip)) {
            $search = array();
            $replace = array();

            /* remove prefix/suffix/namespace items */
            foreach ($this->namespaceStrip as $skipPart => $context) {
                if (in_array('prefix', $context)) {
                    $search[] = '/(\b'.$skipPart.'(?!\b))/';
                    $replace[] = '\\';
                }
                if (in_array('suffix', $context)) {
                    $search[] = '/((?<!\b)'.ucfirst($skipPart).'\b)/';
                    $replace[] = '\\';
                }
                if (in_array('namespace', $context)) {
                    $search[] = '/(\b'.ucfirst($skipPart).'\b)/';
                    $replace[] = '\\';
                }
            }

            /* remove double NS separators */
            $search[] = '|\\\+|';
            $replace[] = '\\';

            /* remove starting/trailing NS separators */
            $search[] = '/(^\\\|\\\$)/';
            $replace[] = '';

            $name = preg_replace($search, $replace, $name);
        }

        if ($this->underscoreify) {
            $name = preg_replace('/(?<=[a-zA-Z0-9])[A-Z]/', '_\\0', $name);
        }

        return strtolower(strtr($name, '\\', '.'));
    }
}
