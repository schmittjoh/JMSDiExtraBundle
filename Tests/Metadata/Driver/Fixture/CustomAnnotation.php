<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture;

use JMS\DiExtraBundle\Annotation\MetadataProcessorInterface;
use JMS\DiExtraBundle\Metadata\ClassMetadata;

/**
 * @Annotation
 */
class CustomAnnotation implements MetadataProcessorInterface
{
    public $key = 'custom';
    public $value;

    /**
     * handle custom metadata for class annotation
     *
     * @param ClassMetadata $metadata
     */
    public function processMetadataForClass(ClassMetadata $metadata)
    {
        $metadata->tags[$this->key] = $this->value;
    }

    /**
     * handle custom metadata for method annotation
     *
     * @param ClassMetadata $metadata
     * @param \ReflectionMethod $method
     */
    public function processMetadataForMethod(ClassMetadata $metadata, \ReflectionMethod $method) {
        $metadata->tags[$this->key] = $this->value;
        $metadata->tags[$this->key . '.target'] = $method->getShortName();
    }
}
