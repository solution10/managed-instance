<?php

namespace Solution10\ManagedInstance\Tests;

use Solution10\ManagedInstance\ManagedInstance;
use PHPUnit_Framework_TestCase;

class ManagedInstanceTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        if (class_exists('\NamedMockInstance')) {
            \NamedMockInstance::clearInstances();
        }
    }

    public function testDefaultInstance()
    {
        $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'MockInstance'
        );

        $instance = \MockInstance::instance();
        $this->assertInstanceOf('MockInstance', $instance);
        $this->assertEquals('default', $instance->instanceName());
    }

    public function testNamedInstances()
    {
        $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'NamedMockInstance'
        );

        $instance = \NamedMockInstance::instance('test');
        $this->assertInstanceOf('NamedMockInstance', $instance);
        $this->assertEquals('test', $instance->instanceName());
    }

    public function testInstanceReuse()
    {
        $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'NamedMockInstance'
        );

        $instance1 = \NamedMockInstance::instance('test');
        $instance1->mark = 'green';

        $instance2 = \NamedMockInstance::instance('test');
        $this->assertEquals($instance1, $instance2);
        $this->assertEquals('green', $instance2->mark);
    }

    public function testSetGetInstanceName()
    {
        $mock = $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'NamedMockInstance'
        );

        $this->assertEquals('default', $mock->instanceName());
        $this->assertEquals($mock, $mock->instanceName('blue'));
        $this->assertEquals('blue', $mock->instanceName());
    }

    public function testInstances()
    {
        $this->getMockForTrait(
            'Solution10\ManagedInstance\ManagedInstance',
            [],
            'NamedMockInstance'
        );

        $default = \NamedMockInstance::instance();
        $blue = \NamedMockInstance::instance('blue');
        $green = \NamedMockInstance::instance('green');

        $instances = \NamedMockInstance::instances();

        $this->assertCount(3, $instances);
        $this->assertEquals($default, $instances['default']);
        $this->assertEquals($blue, $instances['blue']);
        $this->assertEquals($green, $instances['green']);
    }

    public function testRegisterInstance()
    {

    }
}
