<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions
     * are met:
     *
     * 1. Redistributions of source code must retain the above copyright
     *    notice, this list of conditions and the following disclaimer.
     * 2. Redistributions in binary form must reproduce the above copyright
     *    notice, this list of conditions and the following disclaimer in the
     *    documentation and/or other materials provided with the distribution.
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
     * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
     * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
     * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
     * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
     * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
     * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
     * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
     * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
     * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */

    use Brickoo\Library\Storage\Repository;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RepositorTest
     *
     * Test case for the Repository class.
     * @see Brickoo\Library\Storage\Repository
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: RepositoryTest.php 15 2011-12-23 02:05:32Z celestino $
     */

     class RepositoryTest extends PHPUnit_Framework_TestCase
     {

        /**
         * Holds an instance of the Repository class.
         * @var object Repository
         */
        public $Repository;

        /**
         * Set up the LockerFixture object used.
         * @return void
         */
        public function setUp()
        {
            $this->Repository = new Repository();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Storage\Repository::__construct
         * @covers Brickoo\Library\Storage\Repository::clearRepository
         */
        public function testRepositoryConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Storage\Repository',
                ($Repository = new Repository())
            );
        }

        /**
         * Test if the respository is an array.
         * @covers Brickoo\Library\Storage\Repository::getRepository
         */
        public function testGetRepository()
        {
            $this->Repository->commit('someValue');
            $this->Repository->commit('someValue');
            $this->Repository->commit('someValue');
            $this->assertInternalType('array', $this->Repository->getRepository());
            $this->assertArrayHasKey(3, $this->Repository->getRepository());
        }

        /**
        * Test if the respository versions did be increased.
        * @covers Brickoo\Library\Storage\Repository::getRepositoryVersions
        */
        public function testGetRepositoryVersions()
        {
            $this->Repository->commit('someValue');
            $this->Repository->commit('someValue');
            $this->Repository->commit('someValue');
            $this->assertContains(1, $this->Repository->getRepositoryVersions());
            $this->assertContains(2, $this->Repository->getRepositoryVersions());
            $this->assertContains(3, $this->Repository->getRepositoryVersions());
        }

        /**
         * Test if the respository version is available and knowed.
         * @covers Brickoo\Library\Storage\Repository::isVersionAvailable
         */
        public function testIsVersionAvailable()
        {
            $this->Repository->commit('someValue');
            $this->assertTrue($this->Repository->isVersionAvailable(1));
        }

        /**
         * Test if a wrong argument type throws an exception.
         * @covers Brickoo\Library\Storage\Repository::isVersionAvailable
         * @expectedException InvalidArgumentException
         */
        public function testIsVersionAvailableException()
        {
            $this->Repository->isVersionAvailable(array('wrongType'));
        }

        /**
         * Test if the respository can be set to the last version available.
         * @covers Brickoo\Library\Storage\Repository::useLastVersion
         * @covers Brickoo\Library\Storage\Repository::setCurrentVersion
         * @covers Brickoo\Library\Storage\Repository::getCurrentVersion
         */
        public function testUseLastVersion()
        {
            $this->Repository->commit('someValue');
            $this->assertSame($this->Repository,$this->Repository->useLastVersion());
            $this->assertEquals(1, $this->Repository->getCurrentVersion());

            $this->Repository->remove(1);
            $this->Repository->remove(0);
            $this->assertSame($this->Repository,$this->Repository->useLastVersion());
            $this->assertEquals(0, $this->Repository->getCurrentVersion());
        }

        /**
         * Test if the current version has integer type.
         * @covers Brickoo\Library\Storage\Repository::getCurrentVersion
         */
        public function testGetCurrentVersion()
        {
            $this->Repository->commit('someValue');
            $this->assertInternalType('int', $this->Repository->getCurrentVersion());
            $this->assertEquals(1, $this->Repository->getCurrentVersion());
        }

        /**
         * Test if the repository has an locked status.
         * Test if the repository can be locked and unlocked.
         * @covers Brickoo\Library\Storage\Repository::isLocked
         * @covers Brickoo\Library\Storage\Repository::lock
         * @covers Brickoo\Library\Storage\Repository::unlock
         */
        public function testIsLocked()
        {
            $this->assertInternalType('bool', $this->Repository->isLocked());
            $this->assertFalse($this->Repository->isLocked());

            $this->Repository->lock();
            $this->assertTrue($this->Repository->isLocked());

            $this->Repository->unlock();
            $this->assertFalse($this->Repository->isLocked());
        }

        /**
         * Test the amount of versions available.
         * @covers Brickoo\Library\Storage\Repository::count
         */
        public function testMagicFunctionCount()
        {
            $this->assertEquals(1, count($this->Repository));
            $this->Repository->commit('someValue');
            $this->assertEquals(2, count($this->Repository));
        }

        /**
         * Test if the checkout has the specific format.
         * @covers Brickoo\Library\Storage\Repository::checkout
         */
        public function testCheckout()
        {
            $this->assertArrayHasKey('version', $this->Repository->checkout());
            $this->assertArrayHasKey('content', $this->Repository->checkout());
        }

        /**
         * Test if a wrong argument throws an execption.
         * @covers Brickoo\Library\Storage\Repository::checkout
         * @expectedException InvalidArgumentException
         */
        public function testCheckoutArgumentException()
        {
            $this->Repository->checkout('wrongVersionType');
        }

        /**
         * Test if a wrong argument throws an execption.
         * @covers Brickoo\Library\Storage\Repository::checkout
         * @covers Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         * @expectedException Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         */
        public function testCheckoutVersionException()
        {
            $this->Repository->checkout(999);
        }

        /**
         * Test if a commit posible with different arguments.
         * @covers Brickoo\Library\Storage\Repository::commit
         * @covers Brickoo\Library\Storage\Repository::getRecursiveCommit
         */
        public function testCommit()
        {
            $this->assertSame($this->Repository, $this->Repository->commit(array(array('name' => 'brickoo', array(), new stdClass()))));
            $this->assertSame($this->Repository, $this->Repository->commit(array('name' => 'brickoo')));
            $this->assertSame($this->Repository, $this->Repository->commit('brickoo'));
            $this->assertSame($this->Repository, $this->Repository->commit(true));
            $this->assertSame($this->Repository, $this->Repository->commit(new stdClass()));
        }

        /**
         * Test if while the repository is locked commit throws an execption.
         * @covers Brickoo\Library\Storage\Repository::commit
         * @covers Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         * @expectedException Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         */
        public function testCommitLockedException()
        {
            $this->Repository->lock();
            $this->Repository->commit('fail');
        }

        /**
         * Test the restoring of a old version back on stack.
         * @covers Brickoo\Library\Storage\Repository::restore
         */
        public function testRestore()
        {
            $this->Repository->commit('someValue');
            $this->assertEquals(1, $this->Repository->getCurrentVersion());
            $this->assertSame($this->Repository, $this->Repository->restore(0));
            $this->assertEquals(2, $this->Repository->getCurrentVersion());
            $checkout = $this->Repository->checkout();
            $this->assertEquals('initialized', $checkout['content']);
        }

        /**
         * Test if a wrong argument is passed throws an execption.
         * @covers Brickoo\Library\Storage\Repository::restore
         * @expectedException InvalidArgumentException
         */
        public function testRestoreArgumentException()
        {
            $this->Repository->restore('wrongVersionType');
        }

        /**
         * Test if while the repository is locked restore throws an execption.
         * @covers Brickoo\Library\Storage\Repository::restore
         * @covers Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         * @expectedException Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         */
        public function testRestoreLockedException()
        {
            $this->Repository->lock();
            $this->Repository->restore(2);
        }

        /**
         * Test if a version is not available restore throws an execption.
         * @covers Brickoo\Library\Storage\Repository::restore
         * @covers Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         * @expectedException Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         */
        public function testRestoreVersionException()
        {
            $this->Repository->restore(999);
        }

        /**
         * Test if a version can be removed from repository.
         * @covers Brickoo\Library\Storage\Repository::remove
         * @covers Brickoo\Library\Storage\Repository::getCurrentVersion
         */
        public function testRemove()
        {
            $this->Repository->commit('someValue');
            $this->assertEquals(1, $this->Repository->getCurrentVersion());
            $this->assertSame($this->Repository,$this->Repository->remove(1));
            $this->assertEquals(0, $this->Repository->getCurrentVersion());
        }

        /**
         * Test if a wrong argument is passed throws an execption.
         * @covers Brickoo\Library\Storage\Repository::remove
         * @expectedException InvalidArgumentException
         */
        public function testRemoveArgumentException()
        {
            $this->Repository->remove('wrongVersionType');
        }

        /**
         * Test if while the repository is locked remove throws an execption.
         * @covers Brickoo\Library\Storage\Repository::remove
         * @covers Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         * @expectedException Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         */
        public function testRemoveLockedException()
        {
            $this->Repository->commit('someValue');
            $this->Repository->lock();
            $this->Repository->remove(1);
        }

        /**
         * Test if a version is not available remove throws an execption.
         * @covers Brickoo\Library\Storage\Repository::remove
         * @covers Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         * @expectedException Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         */
        public function testRemoveVersionException()
        {
            $this->Repository->remove(999);
        }

        /**
         * Test if a repository backup can be imported and the highest version is recognized.
         * @covers Brickoo\Library\Storage\Repository::import
         * @covers Brickoo\Library\Storage\Repository::setRepository
         * @covers Brickoo\Library\Storage\Repository::checkImportVersions
         */
        public function testImport()
        {
            $data = array(1 => 'commit_1', 2 => 'commit_2', 3 => 'commit_3', 99 => 'commit_99');
            $this->assertSame($this->Repository, $this->Repository->import($data));
            $this->assertEquals(99, $this->Repository->getCurrentVersion());
            $this->assertEquals($this->Repository->getRepository(), $data);
        }

        /**
         * Test if a empty array as argument is passed throws an execption.
         * @covers Brickoo\Library\Storage\Repository::import
         * @expectedException InvalidArgumentException
         */
        public function testImportArgumentException()
        {
            $this->Repository->import(array());
        }

        /**
         * Test if while the repository is locked import throws an execption.
         * @covers Brickoo\Library\Storage\Repository::import
         * @covers Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         * @expectedException Brickoo\Library\Storage\Exceptions\RepositoryLockedException
         */
        public function testImportLockedException()
        {
            $data = array(1 => 'commit_1', 2 => 'commit_2', 3 => 'commit_3');
            $this->Repository->lock();
            $this->Repository->import($data);
        }


        /**
         * Test if a wrong order keys import throws an execption.
         * @covers Brickoo\Library\Storage\Repository::import
         * @covers Brickoo\Library\Storage\Repository::checkImportVersions
         * @covers Brickoo\Library\Storage\Exceptions\InvalidRepositoryStructureException
         * @expectedException Brickoo\Library\Storage\Exceptions\InvalidRepositoryStructureException
         */
        public function testImportOrderException()
        {
            $data = array(1 => 'commit_1', 3 => 'commit_3', 2 => 'commit_2');
            $this->Repository->import($data);
        }

        /**
         * Test if a repository can be exported and returns the initialized value
         * or the latest version commited.
         * @covers Brickoo\Library\Storage\Repository::export
         */
        public function testExport()
        {
            $this->assertEquals('initialized', $this->Repository->export(0));
            $this->Repository->commit('someValue');
            $this->assertEquals('someValue', $this->Repository->export());
        }

        /**
         * Test if a wrong argument is passed throws an execption.
         * @covers Brickoo\Library\Storage\Repository::export
         * @expectedException InvalidArgumentException
         */
        public function testExportArgumentException()
        {
            $this->Repository->export('wrongVersionType');
        }

        /**
         * Test if a version is not available export throws an execption.
         * @covers Brickoo\Library\Storage\Repository::export
         * @covers Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         * @expectedException Brickoo\Library\Storage\Exceptions\VersionNotAvailableException
         */
        public function testExportVersionException()
        {
            $this->Repository->export(999);
        }

    }

?>