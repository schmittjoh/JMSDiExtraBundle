<?php

namespace JMS\DiExtraBundle\Config;

use JMS\DiExtraBundle\Finder\ServiceFinder;
use Symfony\Component\Config\Resource\ResourceInterface;

class ServiceFilesResource implements ResourceInterface
{
    private $files;
    private $bundles;

    public function __construct(array $files, array $bundles)
    {
        $this->files = $files;
        $this->bundles = $bundles;
    }

    public function isFresh($timestamp)
    {
        $finder = new ServiceFinder();
        $files = $finder->findFiles($this->bundles);

        return !array_diff($files, $this->files) && !array_diff($this->files, $files);
    }

    public function __toString()
    {
        return implode(', ', $this->files);
    }

    public function getResource()
    {
        return array($this->files, $this->bundles);
    }
}