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
     * handle custom metadata for annotation
     *
     * @param ClassMetadata $metadata
     */
    public function processMetadata(ClassMetadata $metadata)
    {
        $metadata->tags[$this->key] = $this->value;
    }
}
