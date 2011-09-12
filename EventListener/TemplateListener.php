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

namespace JMS\DiExtraBundle\EventListener;

use CG\Core\ClassUtils;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use JMS\DiExtraBundle\DependencyInjection\LookupMethodClassInterface;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener as FrameworkExtraTemplateListener;

class TemplateListener extends FrameworkExtraTemplateListener
{
    protected function guessTemplateName($controller, Request $request, $engine = 'twig')
    {
        $controllerClass = get_class($controller[0]);
        $userClass = ClassUtils::getUserClass($controllerClass);

        if ($controllerClass === $userClass) {
            return parent::guessTemplateName($controller, $request, $engine);
        }

        if (!preg_match('/Controller\\\(.+)Controller$/', $userClass, $matchController)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a controller class (it must be in a "Controller" sub-namespace and the class name must end with "Controller")', $userClass));
        }

        if (!preg_match('/^(.+)Action$/', $controller[1], $matchAction)) {
            throw new \InvalidArgumentException(sprintf('The "%s" method does not look like an action method (it does not end with Action)', $controller[1]));
        }

        $bundle = $this->getBundleForClass($userClass);

        return new TemplateReference($bundle->getName(), $matchController[1], $matchAction[1], $request->getRequestFormat(), $engine);
    }
}