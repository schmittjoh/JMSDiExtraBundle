<?php

namespace JMS\DiExtraBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\Repository\RepositoryFactory;
use Metadata\MetadataFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Issei Murasawa <issei.m7@gmail.com>
 */
final class ContainerAwareRepositoryFactoryDecorator implements RepositoryFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RepositoryFactory
     */
    private $wrappedFactory;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    public function __construct(ContainerInterface $container, RepositoryFactory $wrappedFactory = null)
    {
        $this->container      = $container;
        $this->wrappedFactory = $wrappedFactory ?: new DefaultRepositoryFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $repository = $this->wrappedFactory->getRepository($entityManager, $entityName);

        if ($repository instanceof ContainerAwareInterface) {
            $repository->setContainer($this->container);

            return $repository;
        }

        if (null !== $metadata = $this->getMetadataFactory()->getMetadataForClass(get_class($repository))) {
            foreach ($metadata->classMetadata as $classMetadata) {
                foreach ($classMetadata->methodCalls as $call) {
                    list($method, $arguments) = $call;
                    call_user_func_array(array($repository, $method), $this->prepareArguments($arguments));
                }
            }
        }

        return $repository;
    }

    private function getMetadataFactory()
    {
        if (!$this->metadataFactory) {
            $this->metadataFactory = $this->container->get('jms_di_extra.metadata.metadata_factory');
        }

        return $this->metadataFactory;
    }

    private function prepareArguments(array $arguments)
    {
        $processed = array();
        foreach ($arguments as $arg) {
            if ($arg instanceof Reference) {
                $processed[] = $this->container->get((string) $arg, $arg->getInvalidBehavior());
            } elseif ($arg instanceof Parameter) {
                $processed[] = $this->container->getParameter((string) $arg);
            } else {
                $processed[] = $arg;
            }
        }

        return $processed;
    }
}
