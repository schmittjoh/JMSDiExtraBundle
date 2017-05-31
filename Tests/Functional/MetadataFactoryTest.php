<?php

namespace JMS\DiExtraBundle\Tests\Functional;

use Metadata\MetadataFactory;

class MetadataFactoryTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testGetMetadataFromMappedSuperClass()
    {
        $metadataFactory = $this->getMetadataFactory();
        
        $this->assertInstanceOf('\Metadata\MetadataFactory', $metadataFactory);
        
        $metadata = $metadataFactory->getMetadataForClass(
            'JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Inheritance\UnmapedSubClass'
        );

        $this->assertCount(2, $metadata->classMetadata);
        $this->assertEquals(
            'JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Inheritance\UnmapedSubClass', 
            $metadata->getOutsideClassMetadata()->name
        );
    }
    
    /**
     * @return MetadataFactory
     */
    private function getMetadataFactory()
    {
        return $this->createClient()->getContainer()->get('jms_di_extra.metadata.metadata_factory');
    }
}
