<?php
/**
 * PHP_CodeCoverage
 *
 * Copyright (c) 2009-2010, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    CodeCoverage
 * @subpackage Tests
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2009-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://github.com/sebastianbergmann/php-code-coverage
 * @since      File available since Release 1.0.0
 */

require_once 'PHP/CodeCoverage/Filter.php';

if (!defined('TEST_FILES_PATH')) {
    define(
      'TEST_FILES_PATH',
      dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .
      '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Tests for the PHP_CodeCoverage_Filter class.
 *
 * @category   PHP
 * @package    CodeCoverage
 * @subpackage Tests
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2009-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://github.com/sebastianbergmann/php-code-coverage
 * @since      Class available since Release 1.0.0
 */
class PHP_CodeCoverage_FilterTest extends PHPUnit_Framework_TestCase
{
    protected $filter;
    protected $files;

    protected function setUp()
    {
        $this->filter = new PHP_CodeCoverage_Filter;

        $this->files = array(
          TEST_FILES_PATH . 'BankAccount.php',
          TEST_FILES_PATH . 'BankAccountTest.php',
          TEST_FILES_PATH . 'CoverageClassExtendedTest.php',
          TEST_FILES_PATH . 'CoverageClassTest.php',
          TEST_FILES_PATH . 'CoverageMethodTest.php',
          TEST_FILES_PATH . 'CoverageNoneTest.php',
          TEST_FILES_PATH . 'CoverageNotPrivateTest.php',
          TEST_FILES_PATH . 'CoverageNotProtectedTest.php',
          TEST_FILES_PATH . 'CoverageNotPublicTest.php',
          TEST_FILES_PATH . 'CoveragePrivateTest.php',
          TEST_FILES_PATH . 'CoverageProtectedTest.php',
          TEST_FILES_PATH . 'CoveragePublicTest.php',
          TEST_FILES_PATH . 'CoveredClass.php',
          TEST_FILES_PATH . 'NamespaceCoverageClassExtendedTest.php',
          TEST_FILES_PATH . 'NamespaceCoverageClassTest.php',
          TEST_FILES_PATH . 'NamespaceCoverageMethodTest.php',
          TEST_FILES_PATH . 'NamespaceCoverageNotPrivateTest.php',
          TEST_FILES_PATH . 'NamespaceCoverageNotProtectedTest.php',
          TEST_FILES_PATH . 'NamespaceCoverageNotPublicTest.php',
          TEST_FILES_PATH . 'NamespaceCoveragePrivateTest.php',
          TEST_FILES_PATH . 'NamespaceCoverageProtectedTest.php',
          TEST_FILES_PATH . 'NamespaceCoveragePublicTest.php',
          TEST_FILES_PATH . 'NamespaceCoveredClass.php',
          TEST_FILES_PATH . 'NotExistingCoveredElementTest.php',
          TEST_FILES_PATH . 'function.php',
          TEST_FILES_PATH . 'source_with_ignore.php',
          TEST_FILES_PATH . 'source_without_ignore.php'
        );
    }

    /**
     * @covers PHP_CodeCoverage_Filter::getInstance
     */
    public function testFactory()
    {
        $filter = PHP_CodeCoverage_Filter::getInstance();
        $this->assertSame($filter, PHP_CodeCoverage_Filter::getInstance());
    }

    /**
     * @covers PHP_CodeCoverage_Filter::addFileToBlacklist
     * @covers PHP_CodeCoverage_Filter::getBlacklist
     */
    public function testAddingAFileToTheBlacklistWorks()
    {
        $this->filter->addFileToBlacklist($this->files[0]);

        $this->assertEquals(
          array('DEFAULT' => array($this->files[0])),
          $this->filter->getBlacklist()
        );
    }

    /**
     * @covers PHP_CodeCoverage_Filter::addFileToBlacklist
     * @covers PHP_CodeCoverage_Filter::getBlacklist
     */
    public function testAddingAFileToTheBlacklistWorks2()
    {
        $this->filter->addFileToBlacklist($this->files[0], 'group');

        $this->assertEquals(
          array('DEFAULT' => array(), 'group' => array($this->files[0])),
          $this->filter->getBlacklist()
        );
    }

    /**
     * @covers            PHP_CodeCoverage_Filter::addFileToBlacklist
     * @expectedException RuntimeException
     */
    public function testAddingAFileToTheBlacklistWorks3()
    {
        $this->filter->addFileToBlacklist('/does/not/exist');
    }

    /**
     * @covers PHP_CodeCoverage_Filter::removeFileFromBlacklist
     * @covers PHP_CodeCoverage_Filter::getBlacklist
     */
    public function testRemovingAFileFromTheBlacklistWorks()
    {
        $this->filter->addFileToBlacklist($this->files[0]);
        $this->filter->removeFileFromBlacklist($this->files[0]);

        $this->assertEquals(
          array('DEFAULT' => array()), $this->filter->getBlacklist()
        );
    }

    /**
     * @covers  PHP_CodeCoverage_Filter::addDirectoryToBlacklist
     * @covers  PHP_CodeCoverage_Filter::getBlacklist
     * @depends testAddingAFileToTheBlacklistWorks
     */
    public function testAddingADirectoryToTheBlacklistWorks()
    {
        $this->filter->addDirectoryToBlacklist(TEST_FILES_PATH);

        $blacklist = $this->filter->getBlacklist();

        foreach (array_keys($blacklist) as $group) {
            sort($blacklist[$group]);
        }

        $this->assertEquals(array('DEFAULT' => $this->files), $blacklist);
    }

    /**
     * @covers            PHP_CodeCoverage_Filter::addDirectoryToBlacklist
     * @expectedException RuntimeException
     * @depends           testAddingAFileToTheBlacklistWorks
     */
    public function testAddingADirectoryToTheBlacklistWorks2()
    {
        $this->filter->addDirectoryToBlacklist('/does/not/exist');
    }

    /**
     * @covers PHP_CodeCoverage_Filter::addFilesToBlacklist
     * @covers PHP_CodeCoverage_Filter::getBlacklist
     */
    public function testAddingFilesToTheBlacklistWorks()
    {
        $this->filter->addFilesToBlacklist(
          File_Iterator_Factory::getFilesAsArray(
            TEST_FILES_PATH, $suffixes = '.php'
          )
        );

        $blacklist = $this->filter->getBlacklist();

        foreach (array_keys($blacklist) as $group) {
            sort($blacklist[$group]);
        }

        $this->assertEquals(array('DEFAULT' => $this->files), $blacklist);
    }

    /**
     * @covers  PHP_CodeCoverage_Filter::removeDirectoryFromBlacklist
     * @covers  PHP_CodeCoverage_Filter::getBlacklist
     * @depends testAddingADirectoryToTheBlacklistWorks
     */
    public function testRemovingADirectoryFromTheBlacklistWorks()
    {
        $this->filter->addDirectoryToBlacklist(TEST_FILES_PATH);
        $this->filter->removeDirectoryFromBlacklist(TEST_FILES_PATH);

        $this->assertEquals(
          array('DEFAULT' => array()), $this->filter->getBlacklist()
        );
    }

    /**
     * @covers            PHP_CodeCoverage_Filter::removeDirectoryFromBlacklist
     * @expectedException RuntimeException
     */
    public function testRemovingADirectoryFromTheBlacklistWorks2()
    {
        $this->filter->removeDirectoryFromBlacklist('/does/not/exist');
    }

    /**
     * @covers PHP_CodeCoverage_Filter::addFileToWhitelist
     * @covers PHP_CodeCoverage_Filter::getWhitelist
     */
    public function testAddingAFileToTheWhitelistWorks()
    {
        $this->filter->addFileToWhitelist($this->files[0]);

        $this->assertEquals(
          array($this->files[0]), $this->filter->getWhitelist()
        );
    }

    /**
     * @covers            PHP_CodeCoverage_Filter::addFileToWhitelist
     * @expectedException RuntimeException
     */
    public function testAddingAFileToTheWhitelistWorks2()
    {
        $this->filter->addFileToWhitelist('/does/not/exist');
    }

    /**
     * @covers PHP_CodeCoverage_Filter::removeFileFromWhitelist
     * @covers PHP_CodeCoverage_Filter::getWhitelist
     */
    public function testRemovingAFileFromTheWhitelistWorks()
    {
        $this->filter->addFileToWhitelist($this->files[0]);
        $this->filter->removeFileFromWhitelist($this->files[0]);

        $this->assertEquals(array(), $this->filter->getWhitelist());
    }

    /**
     * @covers  PHP_CodeCoverage_Filter::addDirectoryToWhitelist
     * @covers  PHP_CodeCoverage_Filter::getWhitelist
     * @depends testAddingAFileToTheWhitelistWorks
     */
    public function testAddingADirectoryToTheWhitelistWorks()
    {
        $this->filter->addDirectoryToWhitelist(TEST_FILES_PATH);

        $whitelist = $this->filter->getWhitelist();
        sort($whitelist);

        $this->assertEquals($this->files, $whitelist);
    }

    /**
     * @covers            PHP_CodeCoverage_Filter::addDirectoryToWhitelist
     * @depends           testAddingAFileToTheWhitelistWorks
     * @expectedException RuntimeException
     */
    public function testAddingADirectoryToTheWhitelistWorks2()
    {
        $this->filter->addDirectoryToWhitelist('/does/not/exist');
    }

    /**
     * @covers PHP_CodeCoverage_Filter::addFilesToWhitelist
     * @covers PHP_CodeCoverage_Filter::getBlacklist
     */
    public function testAddingFilesToTheWhitelistWorks()
    {
        $this->filter->addFilesToWhitelist(
          File_Iterator_Factory::getFilesAsArray(
            TEST_FILES_PATH, $suffixes = '.php'
          )
        );

        $whitelist = $this->filter->getWhitelist();
        sort($whitelist);

        $this->assertEquals($this->files, $whitelist);
    }

    /**
     * @covers  PHP_CodeCoverage_Filter::removeDirectoryFromWhitelist
     * @covers  PHP_CodeCoverage_Filter::getWhitelist
     * @depends testAddingADirectoryToTheWhitelistWorks
     */
    public function testRemovingADirectoryFromTheWhitelistWorks()
    {
        $this->filter->addDirectoryToWhitelist(TEST_FILES_PATH);
        $this->filter->removeDirectoryFromWhitelist(TEST_FILES_PATH);

        $this->assertEquals(array(), $this->filter->getWhitelist());
    }

    /**
     * @covers            PHP_CodeCoverage_Filter::removeDirectoryFromWhitelist
     * @expectedException RuntimeException
     */
    public function testRemovingADirectoryFromTheWhitelistWorks2()
    {
        $this->filter->removeDirectoryFromWhitelist('/does/not/exist');
    }

    /**
     * @covers PHP_CodeCoverage_Filter::isFile
     */
    public function testIsFile()
    {
        $this->assertFalse($this->filter->isFile('eval()\'d code'));
        $this->assertFalse($this->filter->isFile('runtime-created function'));
        $this->assertFalse($this->filter->isFile('assert code'));
        $this->assertFalse($this->filter->isFile('regexp code'));
        $this->assertTrue($this->filter->isFile('filename'));
    }

    /**
     * @covers PHP_CodeCoverage_Filter::isFiltered
     */
    public function testBlacklistedFileIsFiltered()
    {
        $this->filter->addFileToBlacklist($this->files[0]);
        $this->assertTrue($this->filter->isFiltered($this->files[0]));
    }

    /**
     * @covers PHP_CodeCoverage_Filter::isFiltered
     */
    public function testWhitelistedFileIsNotFiltered()
    {
        $this->filter->addFileToWhitelist($this->files[0]);
        $this->assertFalse($this->filter->isFiltered($this->files[0]));
    }

    /**
     * @covers PHP_CodeCoverage_Filter::isFiltered
     */
    public function testNotWhitelistedFileIsFiltered()
    {
        $this->filter->addFileToWhitelist($this->files[0]);
        $this->assertTrue($this->filter->isFiltered($this->files[1]));
    }

    /**
     * @covers            PHP_CodeCoverage_Filter::isFiltered
     * @expectedException InvalidArgumentException
     */
    public function testIsFilteredThrowsExceptionForInvalidArgument()
    {
        $this->filter->isFiltered('foo', array(), NULL);
    }
}
?>