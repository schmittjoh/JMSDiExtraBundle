<?php

namespace JMS\DiExtraBundle\Metadata\Driver;

use JMS\DiExtraBundle\Annotation\AutowireParams;
use JMS\DiExtraBundle\Exception\InvalidTypeException;
use JMS\DiExtraBundle\Annotation\Observe;
use Doctrine\Common\Annotations\Reader;
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

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = new ClassMetadata($className = $class->getName());
        if (false !== $filename = $class->getFilename()) {
            $metadata->fileResources[] = $filename;
        }
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

        $hasPropertyInjection = false;
        foreach ($class->getProperties() as $property) {
            if ($property->getDeclaringClass()->getName() !== $className) {
                continue;
            }
            $name = $property->getName();

            foreach ($this->reader->getPropertyAnnotations($property) as $annot) {
                if ($annot instanceof Autowire) {
                    $hasPropertyInjection = true;
                    $metadata->properties[$name] = $this->convertAutowireValue($name, $annot);
                }
            }
        }

        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() !== $className) {
                continue;
            }
            $name = $method->getName();

            foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                if ($annot instanceof Observe) {
                    $metadata->tags['kernel.event_listener'][] = array(
                        'event' => $annot->event,
                        'method' => $name,
                        'priority' => $annot->priority,
                    );
                } else if ($annot instanceof AutowireParams) {
                    $params = array();
                    foreach ($method->getParameters() as $param) {
                        if (!isset($annot->params[$paramName = $param->getName()])) {
                            $params[$paramName] = $this->convertAutowireValue($paramName, new Autowire(array('value' => null)));
                            continue;
                        }

                        $params[$paramName] = $this->convertAutowireValue($paramName, $annot->params[$paramName]);
                    }

                    if (!$params) {
                        continue;
                    }

                    if ('__construct' === $name) {
                        $metadata->arguments = $params;
                    } else {
                        $metadata->methodCalls[] = array($name, $params);
                    }
                }
            }
        }

        if (null == $metadata->id && !$hasPropertyInjection) {
            return null;
        }

        return $metadata;
    }

    private function convertAutowireValue($name, Autowire $annot)
    {
        if (null === $annot->value) {
            return new Reference($this->generateId($name), false !== $annot->required ? ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE : ContainerInterface::NULL_ON_INVALID_REFERENCE);
        }

        if (false === strpos($annot->value, '%')) {
            return new Reference($annot->value, false !== $annot->required ? ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE : ContainerInterface::NULL_ON_INVALID_REFERENCE);
        }

        return $annot->value;
    }

    private function generateId($name)
    {
        $name = preg_replace('/(?<=[a-zA-Z0-9])[A-Z]/', '_\\0', $name);

        return strtolower(strtr($name, '\\', '.'));
    }
}