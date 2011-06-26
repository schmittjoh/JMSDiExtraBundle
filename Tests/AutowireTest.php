<?php

namespace JMS\DiExtraBundle\Tests;

use \Metadata\MetadataFactory;
use \JMS\DiExtraBundle\Metadata\Driver\AnnotationDriver;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Symfony\Component\DependencyInjection\Definition;
use \Symfony\Component\DependencyInjection\ContainerBuilder;

class AutowireTest extends \PHPUnit_Framework_TestCase
{
    private function buildContainerFor($class, $id, $props = array())
    {
        $container = new ContainerBuilder();
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $metadata = $factory->getMetadataForClass($class);
        $definition = new Definition($class);

        foreach($props as $key => $value)
        {
            $container->set($key, $value);
        }

        foreach ($metadata->classMetadata as $classMetadata) {

        if (null !== $classMetadata->scope) {
                $definition->setScope($classMetadata->scope);
            }
            if (null !== $classMetadata->public) {
                $definition->setPublic($classMetadata->public);
            }
            if (null !== $classMetadata->abstract) {
                $definition->setAbstract($classMetadata->abstract);
            }
            if (null !== $classMetadata->arguments) {
                $definition->setArguments($classMetadata->arguments);
            }

            $definition->setMethodCalls($classMetadata->methodCalls);
            $definition->setTags($classMetadata->tags);
            $definition->setProperties($classMetadata->properties);
        }

        $container->setDefinition($id, $definition);

        return $container;
    }

    public function testAutowire()
    {
        $props = array(
            'form.csrf_provider' => $csrfProvider = new \stdClass,
            'remember_me_services' => $rememberMeServices = new \stdClass,
            'security.context' => $securityContext = new \stdClass,
            'security.authentication.trust_resolver' => $trustResolver = new \stdClass,
            'em' => $em = new \stdClass,
        );

        $id = "login.controller";

        $container = $this->buildContainerFor("\\JMS\\DiExtraBundle\\Tests\\Fixture\\TestController", $id, $props);
        $controller = $container->get($id);

        $this->assertSame($csrfProvider, $controller->getCsrfProvider());
        $this->assertSame($rememberMeServices, $controller->getRememberMeServices());
        $this->assertSame($securityContext, $controller->getSecurityContext());
        $this->assertSame($trustResolver, $controller->getTrustResolver());
        $this->assertSame($em, $controller->getEm());
        $this->assertSame($em, $controller->getEm2());

    }
}
