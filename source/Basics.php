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

trait Basics
{

    protected function getPkgAging($timePkg)
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

    protected function getPkgTiming($value, $defaultNA)
    {
        if (isset($value['time'])) {
            return [
                'Aging'           => $this->getPkgAging($value['time']),
                'Time'            => date('l, d F Y H:i:s', strtotime($value['time'])),
                'Time as PHP no.' => strtotime($value['time']),
                'Time as SQL'     => date('Y-m-d H:i:s', strtotime($value['time'])),
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

    protected function getPkgVersion($value, $defaultNA)
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
