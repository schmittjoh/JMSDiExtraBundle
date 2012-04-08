Doctrine Integration
====================

.. versionadded : 1.1
    Doctrine Integration was added.
    
Configuration
-------------
For BC reasons, Doctrine integration is disabled by default. However, you can
easily enable it in your configuration:

.. configuration-block ::

    .. code-block :: yaml
    
        jms_di_extra:
            doctrine_integration: true
            
    .. code-block :: xml
    
        <jms-di-extra doctrine-integration="true" />

.. note :: 

    When enabling Doctrine integration, you might see some failing type-hints.
    That is because the default entity manager class is replaced by a different
    implementation. So, if you have type-hinted ``Doctrine\ORM\EntityManager``
    in your code, you should first switch these type-hints to
    ``Doctrine\Common\Persistence\ObjectManager`` before proceeding.

Injecting Dependencies Into Repositories
----------------------------------------
If you have enabled Doctrine integration, you can now inject dependencies into
repositories using annotations:

.. code-block :: php

    use JMS\DiExtraBundle\Annotation as DI;

    class MyRepository extends EntityRepository
    {
        private $uuidGenerator;
        
        /**
         * @DI\InjectParams({
         *     "uuidGenerator" = @DI\Inject("my_uuid_generator"),
         * })
         */
        public function setUuidGenerator(UUidGenerator $uuidGenerator)
        {
            $this->uuidGenerator = $uuidGenerator;
        }
        
        // ...
    }
