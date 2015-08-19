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
     * handle custom metadata for annotation
     *
     * @param ClassMetadata $metadata
     */
    public function processMetadata(ClassMetadata $metadata);
}
