<?php

namespace JMS\DiExtraBundle\Finder;

use JMS\DiExtraBundle\Exception\RuntimeException;
use Symfony\Component\Process\ExecutableFinder;

class ServiceFinder
{
    const PATTERN = 'JMS\DiExtraBundle\Annotation';

    private $grepPath;

    public function __construct()
    {
        $finder = new ExecutableFinder();
        $this->grepPath = $finder->find('grep');
    }

    public function findFiles(array $bundles)
    {
        // check for grep availability
        if (null !== $this->grepPath) {
            return $this->findUsingGrep($bundles);
        }

        // TODO: Add FINDSTR support for Windows

        // this should really be avoided at all costs since it is damn slow
        return $this->findUsingFinder($bundles);
    }

    private function findUsingGrep(array $bundles, $grepPath)
    {
        $cmd = $this->grepPath.' --fixed-strings --directories=recurse --devices=skip --files-with-matches --with-filename --max-count=1 --color=never --exclude-dir=\.git --exclude-dir=Resources --include=*.php';
        $cmd .= ' '.escapeshellarg(self::PATTERN);

        foreach ($bundles as $bundle) {
            $cmd .= ' '.escapeshellarg($bundle->getPath());
        }
        exec($cmd, $files, $exitCode);

        if (0 !== $exitCode) {
            throw new RuntimeException(sprintf('Command "%s" exited with non-successful status code "%d".', $cmd, $exitCode));
        }

        return $files;
    }

    private function findUsingFinder(array $bundles)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->name('*.php')
            ->in(array_map(function($bundle) {
                    return $bundle->getPath();
                }, $bundles
            ))
            ->ignoreVCS(true)
            ->filter(function($file) {
                return false !== strpos(file_get_contents($file->getPathName()), self::PATTERN);
            })
        ;

        return array_keys(iterator_to_array($finder));
    }
}