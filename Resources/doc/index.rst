========
Overview
========

This bundle allows you to configure dependency injection using annotations.

Installation
------------
Add the following to your ``deps`` file::

    [JMSDiExtraBundle]
        git=https://github.com/schmittjoh/JMSDiExtraBundle.git
        target=/bundles/JMS/DiExtraBundle
        
    ; Dependencies:
    ;--------------
    [metadata]
        git=https://github.com/schmittjoh/metadata.git
        version=1.1.0 ; <- make sure to get 1.1, not 1.0

Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\DiExtraBundle\JMSDiExtraBundle($this),
        // ...
    );

In addition, this bundle also requires the JMSAopBundle. See its documentation for
installation instructions::

    https://github.com/schmittjoh/JMSAopBundle/blob/master/Resources/doc/index.rst


Make sure that you also register the namespaces with the autoloader::

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'JMS'              => __DIR__.'/../vendor/bundles',
        'Metadata'         => __DIR__.'/../vendor/metadata/src',
        // ...
    ));    


Configuration
-------------
For optimal development performance (in production there is no difference either way), 
it is recommended to explicitly configure the directories which should be scanned for 
service classes (by default no directory is scanned)::

    jms_di_extra:
        locations:
            all_bundles: false
            bundles: [FooBundle, AcmeBlogBundle, etc.]
            directories: [%kernel.root_dir%/../src, some/other/dir]

Usage
-----

Non-Controller Classes
~~~~~~~~~~~~~~~~~~~~~~

Non-controller classes are configured, and managed by Symfony's DIC just like any
other service that you configure using YML, XML, or PHP. The only difference is
that you can do it via annotations which is a lot more convenient.

You can use these annotations on services (for examples, see below):
@Service, @Inject, @InjectParams, @Observe, @Tag

Note that you cannot use the @Inject annotation on private, or protected properties.
Likewise, the @InjectParams annotation does not work on protected, or private methods.


Controllers
~~~~~~~~~~~

Controllers are a special type of class which is also treated specially by this
bundle. The most notable difference is that you do not need to define these
classes as services. Yes, no services, but don't worry you can still use all of
the DIC's features, and even some more.

- Constructor/Setter Injection::

    <?php
   
    use JMS\DiExtraBundle\Annotation as DI;
   
    class Controller
    {
        private $em;
        private $session;
    
        /**
         * @DI\InjectParams({
         *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
         *     "session" = @DI\Inject("session")
         * })
         */
        public function __construct($em, $session)
        {
            $this->em = $em;
            $this->session = $session;
        }
        // ... some actions
    }
    
  **Note:** Constructor Injection is not possible when a parent definition
  also defines a constructor which is configured for injection.

- Property Injection::

    <?php

    use JMS\DiExtraBundle\Annotation as DI;
    
    class Controller
    {
        /** @DI\Inject("doctrine.orm.entity_manager")
        private $em;
        
        /** @DI\Inject("session")
        private $session;
    }
    
- Method/Getter Injection::

    <?php
    
    use JMS\DiExtraBundle\Annotation as DI;
    
    class Controller
    {
        public function myAction()
        {
            // ...
            if ($condition) {
                $mailer = $this->getMailer();
            }
        }
    
        /** @DI\LookupMethod("mailer") */
        protected function getMailer() { /* empty body here */ }
    }

You can use this type of injection if you have a dependency that you do not
always need in the controller, and which is costly to initialize, like the
mailer in the example above.


Annotations
-----------

@Inject
~~~~~~~~~
This marks a property, or parameter for injection::

    use JMS\DiExtraBundle\Annotation\Inject;

    class Controller
    {
        /**
         * @Inject("security.context", required = false)
         */
        private $securityContext;
        
        /**
         * @Inject("%kernel.cache_dir%")
         */
        private $cacheDir;
        
        /**
         * @Inject
         */
        private $session;
    }

If you do not specify the service explicitly, we will try to guess it based on the name
of the property or the parameter.

@InjectParams
~~~~~~~~~~~~~~~
This marks the parameters of a method for injection::

    use JMS\DiExtraBundle\Annotation\Inject;
    use JMS\DiExtraBundle\Annotation\InjectParams;
    use JMS\DiExtraBundle\Annotation\Service;

    /**
     * @Service
     */
    class Listener
    {
        /**
         * @InjectParams({
         *     "em" = @Inject("doctrine.entity_manager")
         * })
         */
        public function __construct(EntityManager $em, Session $session)
        {
            // ...
        }
    }
    
If you don't define all parameters in the param map, we will try to guess which services
should be injected into the remaining parameters based on their name.

@Service
~~~~~~~~
Marks a class as service::

    use JMS\DiExtraBundle\Annotation\Service;

    /**
     * @Service("some.service.id", parent="another.service.id", public=false)
     */
    class Listener
    {
    }

If you do not explicitly define a service id, then we will generated a sensible default
based on the fully qualified class name for you.

@Tag
~~~~
Adds a tag to the service::

    use JMS\DiExtraBundle\Annotation\Service;
    use JMS\DiExtraBundle\Annotation\Tag;

    /**
     * @Service
     * @Tag("doctrine.event_listener", attributes = {"event" = "postGenerateSchema", lazy=true})
     */
    class Listener
    {
        // ...
    }

@Observe
~~~~~~~~
Automatically registers a method as listener to a certain event::

    use JMS\DiExtraBundle\Annotation\Observe;
    use JMS\DiExtraBundle\Annotation\Service;

    /**
     * @Service
     */
    class RequestListener
    {
        /**
         * @Observe("kernel.request", priority = 255)
         */
        public function onKernelRequest()
        {
            // ...
        }
    }

@Validator
~~~~~~~~~~
Automatically registers the given class as constraint validator for the Validator component::

    use JMS\DiExtraBundle\Annotation\Validator;
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\ConstraintValidator;
    
    /**
     * @Validator("my_alias")
     */
    class MyValidator extends ConstraintValidator
    {
        // ...
    }
    
    class MyConstraint extends Constraint
    {
        // ...
        public function validatedBy()
        {
            return 'my_alias';
        }
    }

The @Validator annotation also implies the @Service annotation if you do not specify it explicitly.
The alias which is passed to the @Validator annotation must match the string that is returned from
the ``validatedBy`` method of your constraint.

