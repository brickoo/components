<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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

    use Brickoo\Cache\CacheManager;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * CacheManager
     *
     * Test suite for the CacheManager class.
     * @see Brickoo\Cache\CacheManager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheManagerTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Creates and returns a stub of the LocalCache.
         * @param array $methods the methods to mock
         * @return object LocalCache stub
         */
        public function getLocalCacheStub(array $methods = null)
        {
            return $this->getMock
            (
                'Brickoo\Cache\LocalCache',
                (is_null($methods) ? null : array_values($methods))
            );
        }

        /**
        * Creates and returns a stub of the CacheHandleInterface.
        * @return object CacheProviderInterface stub
        */
        public function getCacheProviderStub()
        {
            return $this->getMock
            (
                'Brickoo\Cache\Provider\Interfaces\CacheProviderInterface',
                array('get', 'set', 'delete', 'flush')
            );
        }

        /**
         * Holds the CacheManager instance used.
         * @var Brickoo\Cache\CacheManager
         */
        protected $CacheManager;

        /**
         * Set up the CacheManager object used.
         * @return void
         */
        protected function setUp()
        {
            $this->CacheManager = new CacheManager($this->getCacheProviderStub());
        }

        /**
         * Test if the CacheHander can be injected as dependency.
         * @covers Brickoo\Cache\CacheManager::__construct
         */
        public function testConstruct()
        {
            $CacheProviderStub = $this->getCacheProviderStub();
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Interfaces\CacheManagerInterface',
                ($CacheManager = new CacheManager($CacheProviderStub))
            );
            $this->assertAttributeSame($CacheProviderStub, '_CacheProvider', $CacheManager);
        }

        /**
         * Test if the CacheHander dependency can be retrieved.
         * @covers Brickoo\Cache\CacheManager::CacheProvider
         */
        public function testGetCacheProvider()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Provider\Interfaces\CacheProviderInterface',
                $this->CacheManager->CacheProvider()
            );
        }

        /**
         * Test if the LocalCache can be injected as dependency and the CacheManager reference is returned.
         * @covers Brickoo\Cache\CacheManager::LocalCache
         * @covers Brickoo\Cache\CacheManager::getDependency
         */
        public function testInjectLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub();
            $this->assertSame($this->CacheManager, $this->CacheManager->LocalCache($LocalCacheStub));
            $this->assertAttributeContains($LocalCacheStub, 'dependencies', $this->CacheManager);
        }

        /**
         * Test if trying to retrieve the not available LocalCache it will be created.
         * @covers Brickoo\Cache\CacheManager::LocalCache
         * @covers Brickoo\Cache\CacheManager::getDependency
         */
        public function testGetLocalCacheLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Interfaces\LocalCacheInterface',
                ($LocalCache = $this->CacheManager->LocalCache())
            );
            $this->assertAttributeContains($LocalCache, 'dependencies', $this->CacheManager);
        }

        /**
         * Test if the LocalCache is used to return the cached content.
         * @covers Brickoo\Cache\CacheManager::get
         */
        public function testGetWithLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'get'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->with('some_identifier')
                           ->will($this->returnValue(true));
            $LocalCacheStub->expects($this->once())
                           ->method('get')
                           ->with('some_identifier')
                           ->will($this->returnValue('local cache content'));

            $this->CacheManager->LocalCache($LocalCacheStub);

            $this->assertEquals('local cache content', $this->CacheManager->get('some_identifier'));
        }

        /**
         * Test if the local cache be called to flush the cache.
         * @covers Brickoo\Cache\CacheManager::flushLocalCache
         */
        public function testFlushLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('flush'));
            $LocalCacheStub->expects($this->once())
                           ->method('flush')
                           ->will($this->returnSelf());

            $this->CacheManager->LocalCache($LocalCacheStub);

            $this->assertNull($this->CacheManager->flushLocalCache());
        }

        /**
         * Test if the local cache can be enabled and the CacheManager reference is returned.
         * @covers Brickoo\Cache\CacheManager::enableLocalCache
         */
        public function testEnableLocalCache()
        {
            $this->assertSame($this->CacheManager, $this->CacheManager->enableLocalCache());
            $this->assertAttributeEquals(true, 'enableLocalCache', $this->CacheManager);
        }


        /**
         * Test if the local cache can be disabled and the CacheManager reference is returned.
         * @covers Brickoo\Cache\CacheManager::disableLocalCache
         */
        public function testDisableLocalCache()
        {
            $this->assertSame($this->CacheManager, $this->CacheManager->disableLocalCache());
            $this->assertAttributeEquals(false, 'enableLocalCache', $this->CacheManager);
        }

        /**
         * Test if the local cache is enabled by default.
         * @covers Brickoo\Cache\CacheManager::isLocalCacheEnabled
         */
        public function testIsLocalCacheEnabled()
        {
            $this->assertTrue($this->CacheManager->isLocalCacheEnabled());
        }

        /**
         * Test if the CacheProvider is used to return the cached content.
         * @covers Brickoo\Cache\CacheManager::get
         */
        public function testGetWithCacheProvider()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(false));

            $CacheProviderStub = $this->CacheManager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue('cache provider content'));

            $this->CacheManager->LocalCache($LocalCacheStub);;

            $this->assertEquals('cache provider content', $this->CacheManager->get('some_identifier'));
        }

        /**
         * Test if the CacheProvider is used to return the cached content with the LocalCache disabled.
         * @covers Brickoo\Cache\CacheManager::get
         */
        public function testGetWithCacheProviderWithoutLocalCache()
        {
            $CacheProviderStub = $this->CacheManager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue('cache provider content'));

            $this->CacheManager->disableLocalCache();

            $this->assertEquals('cache provider content', $this->CacheManager->get('some_identifier'));
        }

        /**
         * Test is trying to retrieve a cached value with a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\CacheManager::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException()
        {
            $this->CacheManager->get(array('wrongType'));
        }

        /**
         * Test if adding a content to cache the LocalCache and CacheProvider are called and the
         * CacheManager refrence is returned.
         * @covers Brickoo\Cache\CacheManager::set
         */
        public function testSet()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('set'));
            $LocalCacheStub->expects($this->once())
                           ->method('set')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->CacheManager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('set')
                              ->will($this->returnSelf());

            $this->CacheManager->LocalCache($LocalCacheStub);

            $this->assertSame
            (
                $this->CacheManager,
                $this->CacheManager->set('some_identifier', array('content'), 60)
            );
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\CacheManager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetIdentifierArgumentException()
        {
            $this->CacheManager->set(array('wrongType'), '', 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Cache\CacheManager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetLifetimeArgumentException()
        {
            $this->CacheManager->set('some_identifier', '', 'wrongType');
        }

        /**
         * Test if trying to delete some content the LocalCache and CacheProvider are called
         * and the CacheManager reference is returned.
         * @covers Brickoo\Cache\CacheManager::delete
         */
        public function testDelete()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'remove'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(true));
            $LocalCacheStub->expects($this->once())
                           ->method('remove')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->CacheManager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                             ->method('delete')
                             ->will($this->returnSelf());

            $this->CacheManager->LocalCache($LocalCacheStub);

            $this->assertSame($this->CacheManager, $this->CacheManager->delete('some_identifier'));
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\CacheManager::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException()
        {
            $this->CacheManager->delete(array('wrongType'));
        }

        /**
         * Test if trying to flush the cache the LocalCache and CacheProvider are called
         * and the CacheManager reference is returned.
         * @covers Brickoo\Cache\CacheManager::flush
         */
        public function testFlush()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('flush'));
            $LocalCacheStub->expects($this->once())
                           ->method('flush')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->CacheManager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                             ->method('flush')
                             ->will($this->returnSelf());

            $this->CacheManager->LocalCache($LocalCacheStub);

            $this->assertSame($this->CacheManager, $this->CacheManager->flush());
        }

        /**
         * Test if the cache callback returns the value which is stored back to the LocalCache and CacheProvider.
         * @covers Brickoo\Cache\CacheManager::getByCallback
         */
        public function testGetCacheCallback()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'set'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(false));
            $LocalCacheStub->expects($this->once())
                           ->method('set')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->CacheManager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue(false));
            $CacheProviderStub->expects($this->once())
                             ->method('set')
                             ->will($this->returnSelf());

            $this->CacheManager->LocalCache($LocalCacheStub);

            $this->assertEquals
            (
                'callback content',
                $this->CacheManager->getByCallback('unique_identifier', array($this, 'callback'), array(), 60)
            );
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\CacheManager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetCacheCallbackIdentifierArgumentException()
        {
            $this->CacheManager->getByCallback(array('wrongType'), 'someFunction', array(), 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Cache\CacheManager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetCacheCallbackLifetimeArgumentException()
        {
            $this->CacheManager->getByCallback('some_identifier', 'someFunction', array(), 'wrongType');
        }

        /**
         * Helper method for the testGetCacheCallback.
         * @returns string the callback response
         */
        public function callback()
        {
            return 'callback content';
        }

    }