<?php

namespace JMS\DiExtraBundle\Metadata\Driver;

use Annotations\ReaderInterface;
use JMS\DiExtraBundle\Annotation\Autowire;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\DiExtraBundle\Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = new ClassMetadata($className = $class->getName());
        foreach ($this->reader->getClassAnnotations($class) as $annot) {
            if ($annot instanceof Service) {
                if (null === $annot->id) {
                    $metadata->id = $this->generateId($className);
                } else {
                    $metadata->id = $annot->id;
                }

                $metadata->parent = $annot->parent;
                $metadata->public = $annot->public;
                $metadata->scope = $annot->scope;
                $metadata->abstract = $annot->abstract;
            } else if ($annot instanceof Tag) {
                $metadata->tags[$annot->name][] = $annot->attributes;
            }
        }

        if (null === $metadata->id) {
            return null;
        }

        foreach ($class->getProperties() as $name => $property) {
            if ($property->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            foreach ($this->reader->getPropertyAnnotations($property) as $annot) {
                if ($annot instanceof Autowire) {
                    if (null === $annot->value) {
                        $metadata->properties[$name] = new Reference($this->generateId($name), false !== $annot->required ? ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE : ContainerInterface::NULL_ON_INVALID_REFERENCE);
                    } else if (false === strpos($annot->value, '%')) {
                        $metadata->properties[$name] = new Reference($annot->value, false !== $annot->required ? ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE : ContainerInterface::NULL_ON_INVALID_REFERENCE);
                    } else {
                        $metadata->properties[$name] = $annot->value;
                    }
                }
            }
        }

        return $metadata;
    }

    private function generateId($name)
    {
        $name = preg_replace('/(?<=[a-zA-Z0-9])[A-Z]/', '_\\0', $name);

        return strtolower(strtr($name, '\\', '.'));
    }
}