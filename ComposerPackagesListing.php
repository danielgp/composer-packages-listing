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
     * Returns a complete list of packages and respective details from a composer.lock file
     *
     * @param string $fileToRead
     * @return array
     */
    protected function getPackageDetailsFromGivenComposerLockFile($fileToRead)
    {
        if (!file_exists($fileToRead)) {
            return ['error' => $fileToRead . ' was not found'];
        }
        $dNA      = '---';
        $alnfo    = [];
        $packages = $this->getPkgFileInListOfPackageArrayOut($fileToRead);
        foreach ($packages['packages'] as $value) {
            $basic                 = $this->getPkgBasicInfo($value, $dNA);
            $atr                   = $this->getPkgOptAtributeAll($value, $dNA);
            $alnfo[$value['name']] = array_merge($basic, $atr);
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
            'Aging'            => (isset($value['time']) ? $this->getPkgAging($value['time']) : $defaultNA),
            'License'          => (isset($value['license']) ? $this->getPkgLcns($value['license']) : $defaultNA),
            'Notification URL' => (isset($value['version']) ? $value['notification-url'] : $defaultNA),
            'Package Name'     => $value['name'],
            'PHP required'     => (isset($value['require']['php']) ? $value['require']['php'] : $defaultNA),
            'Product'          => explode('/', $value['name'])[1],
            'Time'             => (isset($value['time']) ? date('l, d F Y H:i:s', strtotime($value['time'])) : ''),
            'Time as PHP no.'  => (isset($value['time']) ? strtotime($value['time']) : ''),
            'Vendor'           => explode('/', $value['name'])[0],
            'Version no.'      => (isset($value['version']) ? $this->getPkgVerNo($value['version']) : $defaultNA),
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
}
