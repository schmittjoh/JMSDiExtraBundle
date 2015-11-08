<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Metadata\ClassMetadata;

/**
 * enable custom annotations for dependency injection
 *
 * this can be used for both method and class annotations
 */
interface MetadataProcessorInterface
{
    /**
     * handle custom metadata for class annotation
     *
     * @param ClassMetadata $metadata
     */
    public function processMetadataForClass(ClassMetadata $metadata);

    /**
     * handle custom metadata for method annotation
     *
     * @param ClassMetadata $metadata
     * @param \ReflectionMethod $method
     */
    public function processMetadataForMethod(ClassMetadata $metadata, \ReflectionMethod $method);
}
