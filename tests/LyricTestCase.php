<?php

namespace LyricTests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;

abstract class LyricTestCase extends TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Execute protected methods of the class
     *
     * @param $object
     * @param $method
     * @param array $args
     */
    protected function invokeProtectedMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Set protected property
     *
     * @param $object
     * @param $propertyName
     * @param $value
     */
    protected function setProtectedProperty($object, $propertyName, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));

        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }
}
