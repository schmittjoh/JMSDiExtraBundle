<?php

namespace JMS\DiExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Metadata\MetadataFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class DiAwareObjectManager implements ObjectManager
{
    protected $container;
    private $delegate;

    public function __construct(ObjectManager $objectManager, ContainerInterface $container)
    {
        $this->delegate = $objectManager;
        $this->container = $container;
    }

    public function getRepository($className)
    {
        $repository = $this->delegate->getRepository($className);

        if (null !== $metadata = $this->getDiMetadataFactory()
                        ->getMetadataForClass(get_class($repository))) {
            // TODO: Consider switching to mergeable metadata instead
            foreach ($metadata->classMetadata as $classMetadata) {
                foreach ($classMetadata->methodCalls as $call) {
                    list($method, $arguments) = $call;
                    call_user_func_array(array($repository, $method), $this->prepareArguments($arguments));
                }
            }
        }

        return $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function find($className, $id)
    {
        return $this->delegate->find($className, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function persist($object)
    {
        $this->delegate->persist($object);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($object)
    {
        $this->delegate->remove($object);
    }

    /**
     * {@inheritDoc}
     */
    public function merge($object)
    {
        $this->delegate->merge($object);
    }

    /**
     * {@inheritDoc}
     */
    public function clear($objectName = null)
    {
        $this->delegate->clear($objectName);
    }

    /**
     * {@inheritDoc}
     */
    public function detach($object)
    {
        $this->delegate->detach($object);
    }

    /**
     * {@inheritDoc}
     */
    public function refresh($object)
    {
        $this->delegate->refresh($object);
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->delegate->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClassMetadata($className)
    {
        return $this->delegate->getClassMetadata($className);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFactory()
    {
        return $this->delegate->getMetadataFactory();
    }

    /**
     * {@inheritDoc}
     */
    public function initializeObject($obj)
    {
        $this->delegate->initializeObject($obj);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($object)
    {
        return $this->delegate->contains($object);
    }

    /**
     * Delegate any other methods that are not defined on the interface, but in
     * the actual implementation.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->delegate, $method), $args);
    }

    /**
     * @return MetadataFactory
     */
    private function getDiMetadataFactory()
    {
        return $this->container->get('jms_di_extra.metadata.metadata_factory');
    }

    private function prepareArguments(array $arguments)
    {
        $processed = array();
        foreach ($arguments as $arg) {
            if ($arg instanceof Reference) {
                $processed[] = $this->container->get((string) $arg, $arg->getInvalidBehavior());
            } else if ($arg instanceof Parameter) {
                $processed[] = $this->container->getParameter((string) $arg);
            } else {
                $processed[] = $arg;
            }
        }

        return $processed;
    }
}