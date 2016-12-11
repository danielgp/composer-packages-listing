<?php

/**
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Daniel Popiniuc
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
class ComposerPackagesListingTest extends PHPUnit_Framework_TestCase
{

    use \danielgp\composer_packages_listing\ComposerPackagesListing;

    public function testGetPackageDetailsFromGivenComposerLockFile()
    {
        $pathParts = explode(DIRECTORY_SEPARATOR, __DIR__);
        $file      = implode(DIRECTORY_SEPARATOR, array_diff($pathParts, ['tests', 'php-unit']))
                . DIRECTORY_SEPARATOR . 'composer.lock';
        $actual    = $this->getPackageDetailsFromGivenComposerLockFile($file);
        $this->assertArrayHasKey('Aging', $actual['gettext/gettext']);
    }

    public function testGetPackageDetailsFromGivenComposerLockFileError()
    {
        $actual = $this->getPackageDetailsFromGivenComposerLockFile('composer.not');
        $this->assertArrayHasKey('error', $actual);
    }
}
