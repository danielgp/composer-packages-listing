<?php

/**
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 - 2018 Daniel Popiniuc
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
 * useful functions to get quick results
 *
 * @author Daniel Popiniuc
 */
trait ComposerPackagesListing
{

    use Basics;

    /**
     * Decision between Main or Development packages
     *
     * @param array $inParametersArray
     * @return string
     */
    private function decisionPackageOrPackageDev($inParametersArray)
    {
        $sReturn = 'packages';
        if (array_key_exists('Dev', $inParametersArray)) {
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
    protected function exposePhpDetails($skipAging = false)
    {
        $aReturn = [
            'Aging'           => $this->getPkgAging($this->getFileModifiedTimestampOfFile(PHP_BINARY, 'Y-m-d')),
            'Architecture'    => (PHP_INT_SIZE === 4 ? 'x86 (32 bit)' : 'x64 (64 bit)'),
            'Description'     => 'PHP is a popular general-purpose scripting language'
            . ' that is especially suited to web development',
            'Homepage'        => 'https://secure.php.net/',
            'License'         => 'PHP License v3.01',
            'Package Name'    => 'ZendEngine/PHP',
            'Product'         => 'PHP',
            'Time'            => $this->getFileModifiedTimestampOfFile(PHP_BINARY, 'l, d F Y H:i:s'),
            'Time as PHP no.' => $this->getFileModifiedTimestampOfFile(PHP_BINARY, 'PHPtime'),
            'Time as SQL'     => $this->getFileModifiedTimestampOfFile(PHP_BINARY, 'Y-m-d H:i:s'),
            'Type'            => 'scripting language',
            'Url'             => 'https://github.com/php/php-src',
            'Vendor'          => 'The PHP Group',
            'Version'         => PHP_VERSION,
            'Version no.'     => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
        ];
        if ($skipAging) {
            unset($aReturn['Aging']);
        }
        return $aReturn;
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
     * (kept for compatibility reason)
     *
     * @param string $fileIn
     * @param boolean $devInstead true for Development, false for Production
     * @param boolean $skipAging true for skipping, false for not
     * @return array
     */
    protected function getPackageDetailsFromGivenComposerLockFile($fileIn, $devInstead = false, $skipAging = false)
    {
        $inParametersArray = [];
        if ($devInstead) {
            $inParametersArray['Dev'] = true;
        }
        if ($skipAging) {
            $inParametersArray['Skip Aging'] = true;
        }
        return $this->getPackageDetailsFromGivenComposerLockFileEnhanced($fileIn, $inParametersArray);
    }

    /**
     * Returns a complete list of packages and respective details from a composer.lock file
     *
     * @param string $fileIn
     * @param array $inParametersArray
     * @return array
     */
    protected function getPackageDetailsFromGivenComposerLockFileEnhanced($fileIn, $inParametersArray = [])
    {
        if (!file_exists($fileIn)) {
            return ['error' => $fileIn . ' was not found'];
        }
        $alnfo    = [];
        $packages = $this->getPkgFileInListOfPackageArrayOut($fileIn);
        $pkgType  = $this->decisionPackageOrPackageDev($inParametersArray);
        foreach ($packages[$pkgType] as $key => $value) {
            $keyToUse = $value['name'];
            if (array_key_exists('Not Grouped By Name', $inParametersArray)) {
                $keyToUse = $key;
            }
            $alnfo[$keyToUse] = $this->mergePackageAttributes($value, $inParametersArray);
            ksort($alnfo[$keyToUse]);
        }
        ksort($alnfo);
        return $alnfo;
    }

    private function getPkgFileInListOfPackageArrayOut($fileToRead)
    {
        $handle       = fopen($fileToRead, 'r');
        $fileContents = fread($handle, filesize($fileToRead));
        fclose($handle);
        return json_decode($fileContents, true);
    }

    private function mergePackageAttributes($value, $inParametersArray)
    {
        $atr   = $this->getPkgOptAtributeAll($value, '---');
        $basic = $this->getPkgBasicInfo($value, '---');
        $vrs   = $this->getPkgVersion($value, '---');
        $tmng  = $this->getPkgTiming($value, '---');
        if (array_key_exists('Skip Aging', $inParametersArray)) {
            unset($tmng['Aging']);
        }
        return array_merge($atr, $basic, $vrs, $tmng);
    }
}
