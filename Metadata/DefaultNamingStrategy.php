<?php

namespace JMS\DiExtraBundle\Metadata;

class DefaultNamingStrategy implements NamingStrategy
{
    /**
     * Configuration list of namespace or class name components to be stripped from the final service name.
     * The structure should be: ['component' => ['prefix', 'namespace', 'suffix']].
     *
     * @var  array
     */
    private $namespaceStrip;

    /**
     * An associative array of preg_replace key/value pairs.
     *
     * @var array|string[]
     */
    private $stripRules;

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

        if (!empty($this->namespaceStrip) and empty($this->stripRules)) {
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

            $this->stripRules = array_combine($search, $replace);
        }
    }


    /**
     * Returns a service name for an annotated class.
     *
     * @param string $class The fully-qualified class name.
     *
     * @return string A service name.
     */
    public function classToServiceName($name)
    {
        if (!empty($this->stripRules)) {
            $name = preg_replace(array_keys($this->stripRules), array_values($this->stripRules), $name);
        }

        if ($this->underscoreify) {
            $name = preg_replace('/(?<=[a-zA-Z0-9])[A-Z]/', '_\\0', $name);
        }

        return strtolower(strtr($name, '\\', '.'));
    }
}
