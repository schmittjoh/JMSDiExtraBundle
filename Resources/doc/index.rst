========
Overview
========

This bundle allows you to configure dependency injection using annotations.

Installation
------------
Checkout a copy of the code::

    git submodule add https://github.com/schmittjoh/DiExtraBundle.git src/JMS/DiExtraBundle

Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\DiExtraBundle\JMSDiExtraBundle($this),
        // ...
    );

This bundle also requires the Metadata library::

    git submodule add https://github.com/schmittjoh/metadata.git vendor/metadata

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
For optimal performance, we recommend to explicitly configure the directories
which should be scanned for service classes (by default no directory is scanned)::

    jms_di_extra:
        locations:
            all_bundles: false
            bundles: [FooBundle, AcmeBlogBundle, etc.]
            directories: [%kernel.root_dir%/../src, some/other/dir]


Usage Tips
----------
In general, it is recommend that you do **not** define your controllers as services
(and all annotations except @Autowire won't work on controllers atm). This is
mainly for performance reasons as we would need to re-compile the entire DIC
each time you modify a controller.

For the same reason, you should not configure services which you change frequently
using annotations, but instead use one of the other formats (yml, xml, or php).

Annotations
-----------

@Autowire
~~~~~~~~~
This marks a property, or parameter for auto-wiring::

    class Controller
    {
        /**
         * @Autowire("security.context", required = false)
         */
        private $securityContext;
        
        /**
         * @Autowire("%kernel.cache_dir%")
         */
        private $cacheDir;
        
        /**
         * @Autowire
         */
        private $session;
    }

If you do not specify the service explicitly, we will try to guess it based on the name
of the property or the parameter.

@AutowireParams
~~~~~~~~~~~~~~~
This marks the parameters of a method for auto-wiring::

    /**
     * @Service
     */
    class Listener
    {
        /**
         * @AutowireParams({
         *     "em" = @Autowire("doctrine.entity_manager")
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

