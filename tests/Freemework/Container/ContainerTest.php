<?php

namespace Tests\Freemework\Container;

use PHPUnit\Framework\TestCase;
use Freemework\Container\Container;
use Freemework\Container\Exception\{NotFoundException, ContainerException};

class ContainerTest extends TestCase
{
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
        self::assertNotNull($value1 = $container->singleton($name));
        self::assertNotNull($value2 = $container->get($name));
        self::assertSame($value1, $value2);

        $container->set($name = \stdClass::class, $value = \stdClass::class);
        self::assertEquals($container->get($name), $container->get($name));
    }

    public function testAutowiring()
    {
        $container = new Container();

        $outer = $container->get(Outer::class);

        self::assertNotNull($outer);
        self::assertInstanceOf(Outer::class, $outer);

        self::assertNotNull($middle = $outer->middle);
        self::assertInstanceOf(Middle::class, $middle);

        self::assertNotNull($inner = $middle->inner);
        self::assertInstanceOf(Inner::class, $inner);
    }

    public function testAutowiringScalarWithDefault()
    {
        $container = new Container();

        $scalar = $container->get(ScalarWithArrayAndDefault::class);

        self::assertNotNull($scalar);
        self::assertEquals($scalar, new ScalarWithArrayAndDefault(new Inner));

        self::assertNotNull($inner = $scalar->inner);
        self::assertInstanceOf(Inner::class, $inner);

        self::assertEquals(10, $scalar->default);
        self::assertEquals([], $scalar->array);
    }
}

class TestClass
{
    public function __invoke()
    {
        return new \stdClass;
    }
}

class Outer
{
    public $middle;
    
    public function __construct(Middle $middle)
    {
        $this->middle = $middle;
    }
}

class Middle
{
    public $inner;
    
    public function __construct(Inner $inner)
    {
        $this->inner = $inner;
    }
}

class Inner
{
    
}

class ScalarWithArrayAndDefault
{
    public $inner;
    public $array;
    public $default;
    
    public function __construct(Inner $inner, array $array = [], $default = 10)
    {
        $this->inner = $inner;
        $this->array = $array;
        $this->default = $default;
    }
}
