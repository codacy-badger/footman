<?php

namespace Alshf;

use ReflectionClass;
use ReflectionParameter;
use ReflectionException;

class Container
{
    /**
     * Reflection Class Object
     *
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * Class arguments
     *
     * @var array
     */
    private $parameters;

    /**
     * Container Constructor
     *
     * @param string $class
     * @param array  $parameters
     * @return void
     */
    public function __construct($class, array $parameters = [])
    {
        $this->parameters = $parameters;

        try {
            $this->reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw $e;
        }
    }

    /**
     * Create new instance of a given class
     *
     * @return object
     */
    public function make()
    {
        try {
            // Get all constructor Arguments in array
            $arguments = $this->args(
                $this->getConstructor()->getParameters(),
                $this->parameters
            );

            // Create New Instance
            return $this->reflection->newInstanceArgs($arguments);
        } catch (ReflectionException $e) {
            throw $e;
        }
    }

    /**
     * Get given Class Constructor
     * @return ReflectionMethod
     */
    public function getConstructor()
    {
        return $this->reflection->getConstructor();
    }

    /**
     * Get All Given Method Arguments
     *
     * @param  array  $arguments
     * @param  array  $parameters
     * @return array
     */
    private function args(array $arguments, array $parameters)
    {
        return array_map(function ($value) use ($parameters) {
            // Check method Arguments exist in the given Array
            if (array_key_exists($value->name, $parameters)) {
                return $parameters[$value->name];
            }

            // Set Default Value if the method arguments are optional
            // or have default values
            if ($value->isDefaultValueAvailable()) {
                return $value->getDefaultValue();
            }
        }, $arguments);
    }
}
