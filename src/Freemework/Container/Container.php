<?php
declare(strict_types=1);

namespace Freemework\Container;

use Psr\Container\ContainerInterface;
use Freemework\Container\Exception\{NotFoundException, ContainerException};
use Closure;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionException;

use function is_null;
use function is_callable;
use function sprintf;
use function get_declared_classes;

class Container implements ContainerInterface
{
	/**
	 * @var array Container entries
	 */
    protected $services = [];

	/**
	 * @var array Singletons or shared instances
	 */
    protected $instances = [];

	/**
	 * @param array $dependencies Default dependencies.
	 */
    public function __construct(array $dependencies = [])
    {
        $this->services = $dependencies;
    }

    /**
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $item = $this->resolve($id);

        if (!($item instanceof ReflectionClass)) {
            return $item;
        }

        return $this->getInstance($item);
    }

    /**
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id) : bool
    {
        try {
            $item = $this->resolve($id);
        } catch (NotFoundException $e) {
            return false;
        }

        if ($item instanceof ReflectionClass) {
            return $item->isInstantiable();
        }

        return isset($item);
    }

    /**
     * @param string $key Identifier of the entry to look for.
     *
     * @param mixed $value Value of the entry to look for.
     * 
     * @return self
     */
    public function set(string $key, $value) : self
    {
        if (isset($this->instances[$key])) {
            unset($this->instances[$key]);
        }

        if(is_null($value)) {
            $value = $key;
        }

        $this->services[$key] = $value;
        return $this;
    }

    private function resolve($id)
    {
        try {
            $name = $id;

            if (isset($this->services[$id])) {
                $name = $this->services[$id];

                if ($name instanceof Closure || is_callable($name)) {
                    return $name();
                }
            }

            return (new ReflectionClass($name));
        } catch (ReflectionException $e) {
            throw new NotFoundException(sprintf('Unable to resolve "%s"', $id));
        }
    }

    private function getInstance(ReflectionClass $reflector)
    {
        /** @var ReflectionMethod | null $constructor */
        $constructor = $reflector->getConstructor();

        if ($reflector->isInterface()) {
            return $this->resolveInterface($reflector);
        }

		if (!$reflector->isInstantiable()) {
			throw new Exception(sprintf('Class (%s) is not instantiable', $reflector->getName()));
		}

        if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0) {
            return $reflector->newInstance();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param array $parameters
     * @return array
     * @throws ContainerException
     */
	public function getDependencies($parameters) : array
	{
        $dependencies = [];
        
        /** @var ReflectionParameter $parameter */
        foreach ($parameters as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } elseif (!is_null($parameter->getClass())) {
                $dependencies[] = $this->get($parameter->getClass()->getName());
            } elseif ($parameter->isArray() && (!$parameter->isOptional() && !$parameter->isDefaultValueAvailable())) {
                $dependencies[] = [];
            } else {
                if (!$parameter->isOptional() && !$parameter->isDefaultValueAvailable()) {
                    throw new ContainerException(sprintf(
                        'Unable to resolve (%s) in service %s',
                        $parameter->getName(),
                        $parameter->getClass()
                    ));
                }
            }
        }

		return $dependencies;
    }

    /**
     * Returns instance implementig the type hinted interface
     * @param ReflectionClass $reflector The interface Reflector
     * @return object Instance implementig the interface
     * @throws NotFoundException
     */
    public function resolveInterface(ReflectionClass $reflector)
    {
        $classes = get_declared_classes();

        foreach ($classes as $class) {
            $declaredReflector = new ReflectionClass($class);
            if ($declaredReflector->implementsInterface($reflector->getName())) {
                return $this->get($declaredReflector->getName());
            }
        }

        throw new NotFoundException(sprintf('Class (%s) not found', $reflector->getName()));
    }

    /**
     * @param string $id Identifier of the entry to look for.
     *
     * @return object Single instance(Old shared)
     */
    public function singleton($id)
    {
        if (!isset($this->instances[$id])) {
            $this->instances[$id] = $this->get($this->services[$id]);
        }

        return $this->instances[$id];
    }
}
