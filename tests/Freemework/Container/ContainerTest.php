<?php

namespace Tests\Freemework\Container;

use PHPUnit\Framework\TestCase;
use Freemework\Container\Container;
use Freemework\Container\Exception\{NotFoundException, ContainerException};

class ContainerTest extends TestCase
{
    public function testInitContainer()
    {
        $dependencies = [];
        $container = new Container($dependencies);
        self::assertEquals(new Container($dependencies), $container);
    }

    public function testPrimitives()
    {
        $container = new Container();

        $container->set($name = \stdClass::class, $value = new \stdClass);
        self::assertEquals($value, $container->get($name));

        $container->set($name = \stdClass::class, $value = function() {return new \stdClass;});
        self::assertEquals(new \stdClass, $container->get($name));

        $foo = new TestClass;
        $container->set($name = \stdClass::class, $value = $foo());
        self::assertEquals(new \stdClass, $container->get($name));
    }

    public function testNotFound()
    {
        $container = new Container();

        self::expectException(NotFoundException::class);
        $container->get('testing');
    }

    public function testSingleton()
    {
        $container = new Container();

        $container->set($name = \stdClass::class, $value = \stdClass::class);
        self::assertSame($container->singleton($name), $container->get($name));

        $container->set($name = \stdClass::class, $value = \stdClass::class);
        self::assertEquals($container->get($name), $container->get($name));
    }
}

class TestClass { public function __invoke() { return new \stdClass; }}