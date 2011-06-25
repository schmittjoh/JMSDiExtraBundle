<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\DiExtraBundle\Tests\Listener;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use JMS\DiExtraBundle\Tests\Fixture\LoginController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\DiExtraBundle\Metadata\Driver\AnnotationDriver;
use Metadata\MetadataFactory;
use JMS\DiExtraBundle\Listener\ControllerInjectionListener;

class ControllerInjectionListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnCoreController()
    {
        $container = new Container();
        $container->set('form.csrf_provider', $csrfProvider = new \stdClass);
        $container->set('remember_me_services', $rememberMeServices = new \stdClass);
        $container->set('security.context', $securityContext = new \stdClass);
        $container->set('security.authentication.trust_resolver', $trustResolver = new \stdClass);

        $listener = $this->getListener($container);
        $controller = new LoginController();

        $this->assertNull($controller->getCsrfProvider());
        $this->assertNull($controller->getRememberMeServices());
        $this->assertNull($controller->getSecurityContext());
        $this->assertNull($controller->getTrustResolver());

        $listener->onCoreController(new FilterControllerEvent(
            $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            array($controller, 'loginAction'),
            Request::create('/'),
            HttpKernelInterface::MASTER_REQUEST
        ));

        $this->assertSame($csrfProvider, $controller->getCsrfProvider());
        $this->assertSame($rememberMeServices, $controller->getRememberMeServices());
        $this->assertSame($securityContext, $controller->getSecurityContext());
        $this->assertSame($trustResolver, $controller->getTrustResolver());
    }

    private function getListener($container)
    {
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));

        return new ControllerInjectionListener($container, $factory);
    }
}