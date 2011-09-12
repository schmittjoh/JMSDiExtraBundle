<?php

namespace JMS\DiExtraBundle\Tests\Functional;

use Symfony\Component\HttpKernel\Util\Filesystem;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    static protected function createKernel(array $options = array())
    {
        return new AppKernel(
            isset($options['config']) ? $options['config'] : 'default.yml'
        );
    }

    protected function tearDown()
    {
        $this->cleanTmpDir();
    }

    protected function setUp()
    {
        $this->cleanTmpDir();
    }

    private function cleanTmpDir()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().'/JMSDiExtraBundle');
    }
}