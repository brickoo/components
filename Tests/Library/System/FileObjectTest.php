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

    use Brickoo\Library\System\FileObject;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * FileObjectTest
     *
     * Test case for the FileObject class.
     * @see Brickoo\Library\System\FileObject
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */
    class FileObjectTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the FileObject class.
         * @var FileObject
         */
        protected $FileObject;

        /**
         * Sets up the FileObject used..
         * This method is called before a test is executed.
         * @return void
         */
        protected function setUp()
        {
            $this->FileObject = new FileObject;
        }

        /**
         * Test if the constructor can be called.
         * @covers Brickoo\Library\System\FileObject::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Library\System\FileObject', $this->FileObject);
        }

        /**
         * Tests if the location can be set and the FileObject reference is returned.
         * @covers Brickoo\Library\System\FileObject::setLocation
         */
        public function testSetLocation()
        {
            $this->assertSame($this->FileObject, $this->FileObject->setLocation('/var/www/test.txt'));
        }

        /**
         * Trying to set a wrong argument type throws an exception.
         * @covers Brickoo\Library\System\FileObject::setLocation
         * @expectedException InvalidArgumentException
         */
        public function testSetLocationArgumentException()
        {
            $this->FileObject->setLocation(array('wrongType'));
        }

        /**
         * Trying to set a new location while resource exists throws an exception.
         * @covers Brickoo\Library\System\FileObject::setLocation
         * @covers Brickoo\Library\System\Exceptions\ResourceAlreadyExistsException
         * @expectedException Brickoo\Library\System\Exceptions\ResourceAlreadyExistsException
         */
        public function testSetLocationResourceException()
        {
            $this->FileObject->open('php://memory', 'r');
            $this->FileObject->setLocation('/other/place/test.txt');
        }

        /**
         * Test if the lcoation can be retrieved
         * @covers Brickoo\Library\System\FileObject::getLocation
         */
        public function testGetLocation()
        {
            $this->FileObject->setLocation(('/var/www/test.txt'));
            $this->assertEquals('/var/www/test.txt', $this->FileObject->getLocation());
        }

        /**
         * Trying to get an not available location throws an exception.
         * @covers Brickoo\Library\System\FileObject::getLocation
         * @expectedException UnexpectedValueException
         */
        public function testGetLocationValueException()
        {
            $this->FileObject->getLocation();
        }

        /**
         * Test if the mode can be retrieved.
         * @covers Brickoo\Library\System\FileObject::getMode
         */
        public function testGetMode()
        {
            $this->FileObject->setMode('r+');
            $this->assertEquals('r+', $this->FileObject->getMode());
        }

        /**
         * Trying to get an not available mode throws an exception.
         * @covers Brickoo\Library\System\FileObject::getMode
         * @expectedException UnexpectedValueException
         */
        public function testGetModeValueException()
        {
            $this->FileObject->getMode();
        }

        /**
         * Test if the mode can be set and the FileObject reference is returned.
         * @covers Brickoo\Library\System\FileObject::setMode
         */
        public function testSetMode()
        {
            $this->assertSame($this->FileObject, $this->FileObject->setMode('w+'));
        }

        /**
         * Trying to set a wrong argument type throws an exception.
         * @covers Brickoo\Library\System\FileObject::setMode
         * @expectedException InvalidArgumentException
         */
        public function testSetModeArgumentException()
        {
            $this->FileObject->setMode(array('wrongType'));
        }

        /**
         * Trying to set a new mode while resource exists throws an exception.
         * @covers Brickoo\Library\System\FileObject::setMode
         * @covers Brickoo\Library\System\Exceptions\ResourceAlreadyExistsException
         * @expectedException Brickoo\Library\System\Exceptions\ResourceAlreadyExistsException
         */
        public function testSetModeResourceException()
        {
            $this->FileObject->open('php://memory', 'r');
            $this->FileObject->setMode('w');
        }

        /**
         * Test if a resource can be created.
         * @covers Brickoo\Library\System\FileObject::open
         */
        public function testOpen()
        {
            $this->assertInternalType('resource', $this->FileObject->open('php://memory', 'r'));
        }

        /**
         * Trying to create a new resource while one exists throws an exception.
         * @covers Brickoo\Library\System\FileObject::open
         * @covers Brickoo\Library\System\Exceptions\ResourceAlreadyExistsException
         * @expectedException Brickoo\Library\System\Exceptions\ResourceAlreadyExistsException
         */
        public function testOpenDuplicateResourceException()
        {
            $this->FileObject->open('php://memory', 'r');
            $this->FileObject->open('php://memory', 'w');
        }

        /**
         * Trying to create a new resource while one exists throws an exception.
         * @covers Brickoo\Library\System\FileObject::open
         * @covers Brickoo\Library\System\Exceptions\UnableToOpenResourceException
         * @expectedException Brickoo\Library\System\Exceptions\UnableToOpenResourceException
         */
        public function testOpenCreateResourceException()
        {
            $this->FileObject->open('php://path/does/not/exist', 'r');
        }

        /**
         * Test if a resource can be returned.
         * @covers Brickoo\Library\System\FileObject::getResource
         */
        public function testGetResource()
        {
            $this->FileObject->setLocation('php://memory');
            $this->FileObject->setMode('r');
            $this->assertInternalType('resource' , $this->FileObject->getResource());
        }

        /**
         * Test is the resource can be checked as available.
         * @covers Brickoo\Library\System\FileObject::hasResource
         */
        public function testHasResource()
        {
            $this->assertFalse($this->FileObject->hasResource());
            $this->FileObject->open('php://memory', 'r');
            $this->assertTrue($this->FileObject->hasResource());
        }

        /**
         * Test is the resource can removed.
         * @covers Brickoo\Library\System\FileObject::removeResource
         */
        public function testRemoveResource()
        {
             $this->FileObject->open('php://memory', 'r');
            $this->assertSame($this->FileObject, $this->FileObject->removeResource());
        }

        /**
         * Test if the instance properties as restored and the object reference ist returned.
         * @covers Brickoo\Library\System\FileObject::clear
         */
        public function testClear()
        {
            $this->assertSame($this->FileObject, $this->FileObject->clear());
        }

        /**
         * Test if the instance removes the resource when the destructor is called.
         * @covers Brickoo\Library\System\FileObject::__destruct
         */
        public function test__destruct()
        {
            $FileObjectStub = $this->getMock('Brickoo\Library\System\FileObject', array('removeResource'));
            $FileObjectStub->expects($this->once())
                           ->method('removeResource')
                           ->will($this->returnSelf());

            $FileObjectStub->__destruct();
        }

        /**
         * Test if the write method can write to the resource location.
         * @covers Brickoo\Library\System\FileObject::write
         */
        public function testWrite()
        {
            $data = 'some data save to file';
            $this->FileObject->setLocation('php://memory');
            $this->FileObject->setMode('w+');
            $this->assertSame($this->FileObject, $this->FileObject->write($data));
        }

        /**
         * Test if the write method with wrong argument throws an exception..
         * @covers Brickoo\Library\System\FileObject::write
         * @expectedException InvalidArgumentException
         */
        public function testWriteArgumentException()
        {
            $this->FileObject->write(array('wrongType'));
        }

        /**
         * Test if the write method with wrong mode used throws an exception.
         * @covers Brickoo\Library\System\FileObject::write
         * @covers Brickoo\Library\System\Exceptions\InvalidModeOperationException
         * @expectedException Brickoo\Library\System\Exceptions\InvalidModeOperationException
         */
        public function testWriteInvalidModeOperationException()
        {
            $this->FileObject->setMode('r');
            $this->FileObject->write('fails');
        }

        /**
         * Test if the read method can read from the resource location.
         * @covers Brickoo\Library\System\FileObject::read
         */
        public function testRead()
        {
            $data = 'some data save to file';
            $this->FileObject->setLocation('php://memory');
            $this->FileObject->setMode('w+');
            $this->FileObject->write($data);

            $this->FileObject->fseek(0);
            $this->assertEquals($data, $this->FileObject->read(strlen($data)));
        }

        /**
         * Test if the read method with wrong argument throws an exception.
         * @covers Brickoo\Library\System\FileObject::read
         * @expectedException InvalidArgumentException
         */
        public function testReadArgumentException()
        {
            $this->FileObject->read('wrongType');
        }

        /**
         * Test if the read method with wrong mode used throws an exception.
         * @covers Brickoo\Library\System\FileObject::read
         * @covers Brickoo\Library\System\Exceptions\InvalidModeOperationException
         * @expectedException Brickoo\Library\System\Exceptions\InvalidModeOperationException
         */
        public function testReadInvalidModeOperationException()
        {
            $this->FileObject->setMode('w');
            $this->FileObject->read(1024);
        }

        /**
         * Test if the close method remove the resource handle.
         * @covers Brickoo\Library\System\FileObject::close
         */
        public function testClose()
        {
            $this->FileObject->setLocation('php://memory');
            $this->FileObject->setMode('r');
            $this->assertInternalType('resource', $this->FileObject->getResource());
            $this->assertSame($this->FileObject, $this->FileObject->close());
        }

        /**
         * Test if the trying to close the resource without being opened throws an exception.
         * @covers Brickoo\Library\System\FileObject::close
         * @covers Brickoo\Library\System\Exceptions\ResourceNotAvailableException
         * @expectedException Brickoo\Library\System\Exceptions\ResourceNotAvailableException
         */
        public function testCloseResourceException()
        {
            $this->FileObject->close();
        }

        /**
         * Test if magic functions can be called an returns the function return value.
         * @covers Brickoo\Library\System\FileObject::__call
         */
        public function test__call()
        {
            $originalData = 'some data to test with magic functions';
            $this->FileObject->setLocation('php://memory');
            $this->FileObject->setMode('w+');
            $this->assertEquals(strlen($originalData), $this->FileObject->fwrite($originalData)); // magic

            $this->assertEquals(0, $this->FileObject->fseek(0)); // magic

            $loadedData = '';
            while(! $this->FileObject->feof()) // magic
            {
                $loadedData .= $this->FileObject->fread(1); // magic
            }
            $this->assertEquals($originalData, $loadedData);
        }

        /**
         * Test if the trying to call fopen() throws an exception.
         * @covers Brickoo\Library\System\FileObject::__call
         * @expectedException BadMethodCallException
         */
        public function testFopenBadMethodCallException()
        {
            $this->FileObject->fopen();
        }

        /**
         * Test if the trying to call fclose() throws an exception.
         * @covers Brickoo\Library\System\FileObject::__call
         * @expectedException BadMethodCallException
         */
        public function testFcloseBadMethodCallException()
        {
            $this->FileObject->fclose();
        }

    }

?>