<?php
namespace JMS\DiExtraBundle\Annotation;

/**
 * Marker interface for custom annotations that add injection.
 *
 * For example, the InjectParams annotation configures injection, whereas the AfterSetup annotation does not.
 */
interface InjectionAnnotationInterface extends MetadataProcessorInterface {

}