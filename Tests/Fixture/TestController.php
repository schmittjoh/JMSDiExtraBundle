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

namespace JMS\DiExtraBundle\Tests\Fixture;

use JMS\DiExtraBundle\Annotation\Autowire;
use JMS\DiExtraBundle\Annotation\AutowireParams;

class TestController
{
    private $em, $em2;

    /**
     * @Autowire("form.csrf_provider")
     */
    private $csrfProvider;

    /**
     * @Autowire
     */
    private $rememberMeServices;

    /**
     * @Autowire("security.context")
     */
    private $securityContext;

    /**
     * @Autowire("security.authentication.trust_resolver")
     */
    private $trustResolver;

    /**
     * @Autowire({
     *  "em" = @Autowire("em"),
     *  "em2" = @Autowire("em")
     * })
     */
    public function __construct($em, $em2)
    {
        $this->em = $em;
        $this->em2 = $em2;
    }

    public function getCsrfProvider()
    {
        return $this->csrfProvider;
    }

    public function getRememberMeServices()
    {
        return $this->rememberMeServices;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function getTrustResolver()
    {
        return $this->trustResolver;
    }

    public function getEm()
    {
        return $this->em;
    }

    public function setEm($em)
    {
        $this->em = $em;
    }

    public function getEm2()
    {
        return $this->em2;
    }

    public function setEm2($em2)
    {
        $this->em2 = $em2;
    }
}
