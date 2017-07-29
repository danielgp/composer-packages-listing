<?php

/**
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Daniel Popiniuc
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace danielgp\composer_packages_listing;

/**
 * usefull functions to get quick results
 *
 * @author Daniel Popiniuc
 */
trait ComposerPackagesListing
{

    /**
     * Decision between Main or Development packages
     *
     * @param boolean $devInstead
     * @return string
     */
    private function decisionPackageOrPackageDev($devInstead)
    {
        $sReturn = 'packages';
        if ($devInstead) {
            $sReturn = 'packages-dev';
        }
        return $sReturn;
    }

    /**
     * Exposes few Environment details
     *
     * @return array
     */
    protected function exposeEnvironmentDetails()
    {
        $knownValues = [
            'AMD64' => 'x64 (64 bit)',
            'i386'  => 'x86 (32 bit)',
            'i586'  => 'x86 (32 bit)',
        ];
        return [
            'Host Name'                     => php_uname('n'),
            'Machine Type'                  => php_uname('m'),
            'Operating System Architecture' => $knownValues[php_uname('m')],
            'Operating System Name'         => php_uname('s'),
            'Operating System Version'      => php_uname('r') . ' ' . php_uname('v'),
        ];
    }

    /**
     *
     * @return array
     */
    protected function exposePhpDetails()
    {
        return [
            'Aging'            => $this->getPkgAging($this->getFileModifiedTimestampOfFile(PHP_BINARY, 'PHPtime')),
            'Description'      => 'PHP is a popular general-purpose scripting language'
            . ' that is especially suited to web development',
            'Homepage'         => 'https://secure.php.net/',
            'License'          => 'PHP License v3.01',
            'Notification URL' => '---',
            'PHP required'     => '---',
            'Package Name'     => 'ZendEngine/PHP',
            'Product'          => 'PHP',
            'Time'             => $this->getFileModifiedTimestampOfFile(PHP_BINARY, 'l, d F Y H:i:s'),
            'Time as PHP no.'  => $this->getFileModifiedTimestampOfFile(PHP_BINARY, 'PHPtime'),
            'Type'             => 'scripting language',
            'Url'              => 'https://github.com/php/php-src',
            'Vendor'           => 'The PHP Group',
            'Version'          => PHP_VERSION,
            'Version no.'      => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
        ];
    }

    /**
     * Returns Modified date and time of a given file
     *
     * @param string $fileName
     * @param string $format
     * @param boolean $resultInUtc
     * @return string
     */
    protected function getFileModifiedTimestampOfFile($fileName, $format = 'Y-m-d H:i:s', $resultInUtc = false)
    {
        if (!file_exists($fileName)) {
            return ['error' => $fileName . ' was not found'];
        }
        $info = new \SplFileInfo($fileName);
        if ($format === 'PHPtime') {
            return $info->getMTime();
        }
        $sReturn = date($format, $info->getMTime());
        if ($resultInUtc) {
            $sReturn = gmdate($format, $info->getMTime());
        }
        return $sReturn;
    }

    /**
     * Returns a complete list of packages and respective details from a composer.lock file
     *
     * @param string $fileToRead
     * @return array
     */
    protected function getPackageDetailsFromGivenComposerLockFile($fileToRead, $devInstead = false)
    {
        if (!file_exists($fileToRead)) {
            return ['error' => $fileToRead . ' was not found'];
        }
        $dNA      = '---';
        $alnfo    = [];
        $packages = $this->getPkgFileInListOfPackageArrayOut($fileToRead);
        foreach ($packages[$this->decisionPackageOrPackageDev($devInstead)] as $value) {
            $atr                   = $this->getPkgOptAtributeAll($value, $dNA);
            $basic                 = $this->getPkgBasicInfo($value, $dNA);
            $vrs                   = $this->getPkgVersion($value, $dNA);
            $alnfo[$value['name']] = array_merge($atr, $basic, $vrs, $this->getPkgTiming($value, $dNA));
            ksort($alnfo[$value['name']]);
        }
        ksort($alnfo);
        return $alnfo;
    }

    private function getPkgAging($timePkg)
    {
        $dateTimeToday = new \DateTime(date('Y-m-d', strtotime('today')));
        $dateTime      = new \DateTime(date('Y-m-d', strtotime($timePkg)));
        $interval      = $dateTimeToday->diff($dateTime);
        return $interval->format('%a days ago');
    }

    private function getPkgBasicInfo($value, $defaultNA)
    {
        return [
            'License'      => (isset($value['license']) ? $this->getPkgLcns($value['license']) : $defaultNA),
            'Package Name' => $value['name'],
            'PHP required' => (isset($value['require']['php']) ? $value['require']['php'] : $defaultNA),
            'Product'      => explode('/', $value['name'])[1],
            'Vendor'       => explode('/', $value['name'])[0],
        ];
    }

    private function getPkgFileInListOfPackageArrayOut($fileToRead)
    {
        $handle       = fopen($fileToRead, 'r');
        $fileContents = fread($handle, filesize($fileToRead));
        fclose($handle);
        return json_decode($fileContents, true);
    }

    private function getPkgLcns($license)
    {
        $lcns = $license;
        if (is_array($license)) {
            $lcns = implode(', ', $license);
        }
        return $lcns;
    }

    private function getPkgOptAtributeAll($value, $defaultNA)
    {
        $attr    = ['description', 'homepage', 'type', 'url', 'version'];
        $aReturn = [];
        foreach ($attr as $valueA) {
            $aReturn[ucwords($valueA)] = $defaultNA;
            if (array_key_exists($valueA, $value)) {
                $aReturn[ucwords($valueA)] = $value[$valueA];
            }
        }
        return $aReturn;
    }

    private function getPkgTiming($value, $defaultNA)
    {
        if (isset($value['time'])) {
            return [
                'Aging'           => $this->getPkgAging($value['time']),
                'Time'            => date('l, d F Y H:i:s', strtotime($value['time'])),
                'Time as PHP no.' => strtotime($value['time']),
            ];
        }
        return ['Aging' => $defaultNA, 'Time' => $defaultNA, 'Time as PHP no.' => $defaultNA];
    }

    private function getPkgVerNo($version)
    {
        $vrs = $version;
        if (substr($version, 0, 1) == 'v') {
            $vrs = substr($version, 1, strlen($version) - 1);
        }
        if (strpos($vrs, '-') !== false) {
            $vrs = substr($vrs, 0, strpos($vrs, '-'));
        }
        return $vrs;
    }

    private function getPkgVersion($value, $defaultNA)
    {
        if (isset($value['version'])) {
            return [
                'Notification URL' => $value['notification-url'],
                'Version no.'      => $this->getPkgVerNo($value['version']),
            ];
        }
        return ['Notification URL' => $defaultNA, 'Version no.' => $defaultNA];
    }
}
