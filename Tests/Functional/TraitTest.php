<?php

namespace JMS\DiExtraBundle\Tests\Functional;

class TraitTest extends BaseTestCase
{
    /**
     * @requires PHP 5.4.0
     * @runInSeparateProcess
     */
    public function testInjectionFromTrait()
    {
        $this->createClient();

        $container = self::$kernel->getContainer();
        $classWithTrait = $container->get('concrete_class_with_trait');
        $templating = $container->get('templating');

        $this->assertSame($templating, $classWithTrait->getTemplating());
    }
}