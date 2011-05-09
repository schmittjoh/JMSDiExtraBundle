<?php

namespace JMS\DiExtraBundle\DependencyInjection\Compiler;

use JMS\DiExtraBundle\Config\ServiceFilesResource;

use Symfony\Component\Config\Resource\FileResource;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Definition;
use JMS\DiExtraBundle\Finder\ServiceFinder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AnnotationConfigurationPass implements CompilerPassInterface
{
    private $kernel;
    private $finder;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->finder = new ServiceFinder();
    }

    public function process(ContainerBuilder $container)
    {
        $reader = $container->get('annotation_reader');
        $factory = $container->get('jms_di_extra.metadata.metadata_factory');

        $files = $this->finder->findFiles($bundles = $this->kernel->getBundles());
        $container->addResource(new ServiceFilesResource($files, $bundles));
        foreach ($files as $file) {
            $container->addResource(new FileResource($file));
            require_once $file;

            $className = $this->getClassName($file);

            try {
                $metadata = $factory->getMetadataForClass($className);
            } catch (\RuntimeException $ex) {
                continue;
            }

            $previous = null;
            foreach ($metadata->classMetadata as $classMetadata) {
                if (null === $previous && null === $classMetadata->parent) {
                    $definition = new Definition($classMetadata->name);
                } else {
                    $definition = new DefinitionDecorator(
                        $classMetadata->parent ?: $previous->id
                    );
                }

                $definition->setClass($classMetadata->name);
                if (null !== $classMetadata->scope) {
                    $definition->setScope($classMetadata->scope);
                }
                if (null !== $classMetadata->public) {
                    $definition->setPublic($classMetadata->public);
                }
                if (null !== $classMetadata->abstract) {
                    $definition->setAbstract($classMetadata->abstract);
                }
                $definition->setTags($classMetadata->tags);
                $definition->setProperties($classMetadata->properties);

                $container->setDefinition($classMetadata->id, $definition);
                $previous = $classMetadata;
            }
        }
    }

    /**
     * Only supports one namespaced class per file
     *
     * @throws \RuntimeException if the class name cannot be extracted
     * @param string $filename
     * @return string the fully qualified class name
     */
    private function getClassName($filename)
    {
        $src = file_get_contents($filename);

        if (!preg_match('/\bnamespace\s+([^;]+);/s', $src, $match)) {
            throw new \RuntimeException(sprintf('Namespace could not be determined for file "%s".', $filename));
        }
        $namespace = $match[1];

        if (!preg_match('/\bclass\s+([^\s]+)\s+(?:extends|implements|{)/s', $src, $match)) {
            throw new \RuntimeException(sprintf('Could not extract class name from file "%s".', $filename));
        }

        return $namespace.'\\'.$match[1];
    }
}