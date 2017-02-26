<?php

namespace Alshf;

use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use Illuminate\Support\Collection;

class Container
{
    private $reflection;

    private $parameters;

    public function __construct($class, array $parameters = [])
    {
        $this->parameters = $parameters;

        try {
            $this->reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw $e;
        }
    }

    public function make()
    {
        try {
            $arguments = $this->args(
                $this->getConstructor()->getParameters(),
                $this->parameters
            );
            return $this->reflection->newInstanceArgs($arguments);
        } catch (ReflectionException $e) {
            throw $e;
        }
    }

    public function getConstructor()
    {
        return $this->reflection->getConstructor();
    }

    private function args(array $arguments, array $parameters)
    {
        return array_map(function ($value) use ($parameters) {
            if (array_key_exists($value->name, $parameters)) {
                return $parameters[$value->name];
            }

            if ($value->isDefaultValueAvailable()) {
                return $value->getDefaultValue();
            }
        }, $arguments);
    }
}
