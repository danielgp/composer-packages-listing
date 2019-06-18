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

namespace danielgp\composer_packages_listing;

class ComposerPackagesListingTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp(): void
    {
        require_once str_replace('tests' . DIRECTORY_SEPARATOR . 'php-unit', 'source', __DIR__)
                . DIRECTORY_SEPARATOR . 'Basics.php';
        require_once str_replace('tests' . DIRECTORY_SEPARATOR . 'php-unit', 'source', __DIR__)
                . DIRECTORY_SEPARATOR . 'ComposerPackagesListing.php';
    }

    public function testGetPackageDetailsFromGivenComposerLockFile()
    {
        $mock        = $this->getMockForTrait(ComposerPackagesListing::class);
        $fileToCheck = str_replace('tests' . DIRECTORY_SEPARATOR . 'php-unit', '', realpath(__DIR__)) . 'composer.lock';
        $actual      = $mock->getPackageDetailsFromGivenComposerLockFile($fileToCheck, true);
        $this->assertArrayHasKey('Aging', $actual['phpunit/phpunit']);
    }

    public function testGetPackageDetailsFromGivenComposerLockFileError()
    {
        $mock   = $this->getMockForTrait(ComposerPackagesListing::class);
        $actual = $mock->getPackageDetailsFromGivenComposerLockFile('composer.not');
        $this->assertArrayHasKey('error', $actual);
    }
}
