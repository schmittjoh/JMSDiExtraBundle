<?php

namespace JMS\DiExtraBundle\Listener;

use Metadata\MetadataFactory;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerInjectionListener
{
    private $container;
    private $metadataFactory;

    public function __construct(ContainerInterface $container, MetadataFactory $metadataFactory)
    {
        $this->container = $container;
        $this->metadataFactory = $metadataFactory;
    }

    public function onCoreController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        if (null === $metadata = $this->metadataFactory->getMetadataForClass(get_class($controller[0]))) {
            return;
        }

        foreach ($metadata->classMetadata as $cMetadata) {
            foreach ($cMetadata->properties as $name => $value) {
                $property = $cMetadata->reflection->getProperty($name);
                $property->setAccessible(true);

                if ($value instanceof Reference) {
                    $value = $this->container->get((string) $value, $value->getInvalidBehavior());
                }
                $property->setValue($controller[0], $value);
            }
        }
    }
}