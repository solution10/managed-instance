<?php

namespace Solution10\ManagedInstance\Tests;

use PHPUnit_Framework_TestCase;
use MockInstance;
use Solution10\ManagedInstance\Exception\InstanceException;

class ManagedInstanceTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'MockInstance'
        );
    }
    
    public function tearDown()
    {
        if (class_exists('MockInstance')) {
            MockInstance::clearInstances();
        }
    }

    public function testRegisterDefaultInstance()
    {
        $instance = new MockInstance();
        $this->assertEquals($instance, $instance->registerInstance());

        $default = MockInstance::instance();
        $this->assertEquals($instance, $default);
    }

    public function testRegisterNamedInstance()
    {
        $named = new MockInstance();
        $this->assertEquals($named, $named->registerInstance('named'));

        $i = new MockInstance();
        $i->registerInstance();

        $default = MockInstance::instance();
        $this->assertNotEquals($named, $default);
        $this->assertEquals($named, MockInstance::instance('named'));
    }

    public function testDefaultInstance()
    {
        $i = new MockInstance();
        $i->registerInstance();

        $instance = MockInstance::instance();
        $this->assertInstanceOf('MockInstance', $instance);
        $this->assertEquals('default', $instance->instanceName());
    }

    public function testNamedInstances()
    {
        $i = new MockInstance();
        $i->registerInstance('test');

        $instance = MockInstance::instance('test');
        $this->assertInstanceOf('MockInstance', $instance);
        $this->assertEquals('test', $instance->instanceName());
    }

    /**
     * @expectedException       \Solution10\ManagedInstance\Exception\InstanceException
     * @expectedExceptionCode   \Solution10\ManagedInstance\Exception\InstanceException::UNKNOWN_INSTANCE
     */
    public function testUnknownInstance()
    {
        MockInstance::instance('unknown');
    }

    public function testInstanceReuse()
    {
        $i = new MockInstance();
        $i->registerInstance('test');

        $instance1 = MockInstance::instance('test');
        $instance1->mark = 'green';

        $instance2 = MockInstance::instance('test');
        $this->assertEquals($instance1, $instance2);
        $this->assertEquals('green', $instance2->mark);
    }

    public function testSetGetInstanceName()
    {
        $mock = new MockInstance();

        $this->assertEquals(null, $mock->instanceName());
        $this->assertEquals($mock, $mock->instanceName('blue'));
        $this->assertEquals('blue', $mock->instanceName());
    }

    public function testInstanceRenaming()
    {
        $i = new MockInstance();
        $i->registerInstance('test1');

        $i->instanceName('test2');

        $caught = false;
        try {
            MockInstance::instance('test1');
        } catch (InstanceException $e) {
            $caught = true;
            $this->assertEquals(InstanceException::UNKNOWN_INSTANCE, $e->getCode());
        }

        $this->assertTrue($caught);
        $this->assertEquals($i, MockInstance::instance('test2'));
    }

    public function testInstances()
    {
        $d = new MockInstance();
        $d->registerInstance();
        $b = new MockInstance();
        $b->registerInstance('blue');
        $g = new MockInstance();
        $g->registerInstance('green');


        $default = MockInstance::instance();
        $blue = MockInstance::instance('blue');
        $green = MockInstance::instance('green');

        $instances = MockInstance::instances();

        $this->assertCount(3, $instances);
        $this->assertEquals($default, $instances['default']);
        $this->assertEquals($blue, $instances['blue']);
        $this->assertEquals($green, $instances['green']);
    }
    
    public function testUnregisterInstance()
    {
        $instance1 = new MockInstance();
        $instance1->registerInstance('i1');

        $regdInstance = MockInstance::instance('i1');
        $this->assertEquals($instance1, $regdInstance);

        // Now unregister and make sure it's gone
        $this->assertEquals($instance1, $instance1->unregisterInstance());
        $this->assertCount(0, MockInstance::instances());
    }

    public function testGuardAgainstCrossPollenation()
    {
        $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'MockInstanceOne'
        );

        $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'MockInstanceTwo'
        );

        $i = new \MockInstanceOne();
        $i->registerInstance();

        // make sure that MockInstanceTwo doesn't know about MockInstanceOne's instances:
        $this->assertCount(1, \MockInstanceOne::instances());
        $this->assertCount(0, \MockInstanceTwo::instances());
    }
}
