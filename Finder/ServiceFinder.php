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
        if (0 === stripos(PHP_OS, 'win')) {
            return $this->findUsingFindstr($dirs);
        }

        // this should really be avoided at all costs since it is damn slow
        return $this->findUsingFinder($dirs);
    }

    private function findUsingFindstr(array $dirs)
    {
        $tmp = implode(';', $dirs);

        $cmd = 'FINDSTR /M /S /L /P';
        $cmd .= ' /D:'.escapeshellarg($tmp);
        $cmd .= ' '.escapeshellarg(self::PATTERN);
        $cmd .= ' *.php';

        exec($cmd, $lines, $exitCode);

        if (0 !== $exitCode) {
            throw new RuntimeException(sprintf('Command "%s" exited with non-successful status code. "%d".', $cmd, $exitCode));
        }

        // Expecting following findstr output format:
        //     C:\matched\dir1:
        // Relative\Path\To\File1.php
        // Relative\Path\To\File2.php
        //     C:\matched\dir2:
        // Relative\Path\To\File3.php
        // Relative\Path\To\File4.php

        $files = array();
        $currentDir = '';
        foreach ($lines as $line) {
            if (in_array($dir = trim($line, ' :'), $dirs)) {
                $currentDir = $dir;
            } elseif ($currentDir && false !== ($file = realpath($currentDir . DIRECTORY_SEPARATOR . $line))) {
                $files[] = $file;
            } else {
                throw new RuntimeException(sprintf('Unexpected output of "%s" command', $cmd));
            }
        }

        return $files;
    }

    private function findUsingGrep(array $dirs)
    {
        $cmd = $this->grepPath.' --fixed-strings --directories=recurse --devices=skip --files-with-matches --with-filename --max-count=1 --color=never --include=*.php';
        $cmd .= ' '.escapeshellarg(self::PATTERN);

        foreach ($dirs as $dir) {
            $cmd .= ' '.escapeshellarg($dir);
        }
        exec($cmd, $files, $exitCode);

        if (1 === $exitCode) {
            return array();
        }

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