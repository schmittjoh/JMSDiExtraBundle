<?php

namespace JMS\DiExtraBundle\Finder;

use JMS\DiExtraBundle\Exception\RuntimeException;
use Symfony\Component\Finder\Finder;
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

    public function findFiles(array $dirs)
    {
        // check for grep availability
        if (null !== $this->grepPath) {
            return $this->findUsingGrep($dirs);
        }

        // use FINDSTR on Windows
        if (false !== stripos(PHP_OS, 'win')) {
            return $this->findUsingFindstr($dirs);
        }

        // this should really be avoided at all costs since it is damn slow
        return $this->findUsingFinder($dirs);
    }

    private function findUsingFindstr(array $dirs)
    {
        $dirs = implode(';', $dirs);

        $cmd = 'FINDSTR /M /S /L /P';
        $cmd .= ' /D:'.escapeshellarg(substr($dirs, 1));
        $cmd .= ' '.escapeshellarg(self::PATTERN);
        $cmd .= ' *.php';

        exec($cmd, $files, $exitCode);

        if (0 !== $exitCode) {
            throw new \RuntimeException(sprintf('Command "%s" exited with non-successful status code. "%d".', $cmd, $exitCode));
        }

        return $files;
    }

    private function findUsingGrep(array $dirs)
    {
        $cmd = $this->grepPath.' --fixed-strings --directories=recurse --devices=skip --files-with-matches --with-filename --max-count=1 --color=never --exclude-dir=\.git --exclude-dir=Resources  --exclude-dir=Tests --exclude-dir=Controller --include=*.php';
        $cmd .= ' '.escapeshellarg(self::PATTERN);

        foreach ($dirs as $dir) {
            $cmd .= ' '.escapeshellarg($dir);
        }
        exec($cmd, $files, $exitCode);

        if (0 !== $exitCode) {
            throw new RuntimeException(sprintf('Command "%s" exited with non-successful status code "%d".', $cmd, $exitCode));
        }

        return $files;
    }

    private function findUsingFinder(array $dirs)
    {
        $finder = new Finder();
        $pattern = self::PATTERN;
        $finder
            ->files()
            ->name('*.php')
            ->in($dirs)
            ->ignoreVCS(true)
            ->filter(function($file) use ($pattern) {
                return false !== strpos(file_get_contents($file->getPathName()), $pattern);
            })
        ;

        return array_keys(iterator_to_array($finder));
    }
}