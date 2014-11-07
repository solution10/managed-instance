<?php

namespace Solution10\ManagedInstance\Tests;

use PHPUnit_Framework_TestCase;
use MockInstance;

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

    public function testDefaultInstance()
    {
        $instance = MockInstance::instance();
        $this->assertInstanceOf('MockInstance', $instance);
        $this->assertEquals('default', $instance->instanceName());
    }

    public function testNamedInstances()
    {
        $instance = MockInstance::instance('test');
        $this->assertInstanceOf('MockInstance', $instance);
        $this->assertEquals('test', $instance->instanceName());
    }

    public function testInstanceReuse()
    {
        $instance1 = MockInstance::instance('test');
        $instance1->mark = 'green';

        $instance2 = MockInstance::instance('test');
        $this->assertEquals($instance1, $instance2);
        $this->assertEquals('green', $instance2->mark);
    }

    public function testSetGetInstanceName()
    {
        $mock = new MockInstance();

        $this->assertEquals('default', $mock->instanceName());
        $this->assertEquals($mock, $mock->instanceName('blue'));
        $this->assertEquals('blue', $mock->instanceName());
    }

    public function testInstances()
    {
        $default = MockInstance::instance();
        $blue = MockInstance::instance('blue');
        $green = MockInstance::instance('green');

        $instances = MockInstance::instances();

        $this->assertCount(3, $instances);
        $this->assertEquals($default, $instances['default']);
        $this->assertEquals($blue, $instances['blue']);
        $this->assertEquals($green, $instances['green']);
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

        $default = MockInstance::instance();
        $this->assertNotEquals($named, $default);
        $this->assertEquals($named, MockInstance::instance('named'));
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
}
