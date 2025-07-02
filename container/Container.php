<?php
declare(strict_types=1);
namespace App\Container;

use App\Container\ContainerInterface;
use App\Container\Exceptions\CannotResolveParameterException;
use App\Container\Exceptions\InvalidArgumentException;
/**
 * Container class that implements a simple Dependency Injection Container.
 * 
 * This class allows you to register services and retrieve them, resolving dependencies automatically.
 * It supports both class names and callable definitions for services.
 */
class Container implements ContainerInterface
{   /**
     * @var array $services
     * 
     * An array to store instantiated services.
     */
    private array $services = [];
    /**
     * @var array $definitions
     * An array to store service definitions.
     * This can be either a class name or a callable that returns an instance of the service.
     */
    private array $definitions = [];
    /**
     * A list of primitive types that can be resolved directly.
     * 
     * @var array
     */
    private const PRIMITIVES = [
        'string',
        'int',
        'float',
        'bool',
        'array',
        'callable',
        'object',
    ];

    /** 
     * @param string   $class Fully qualified class name
     *                        or configuration id
     * 
     * @access public
     * 
     * @return mixed   Return an object or a primitive type
    */
    public function get(string $class): mixed
    {   // Check if the service is already instantiated
        if (isset($this->services[$class])) { // If the service is already instantiated, return it
            return $this->services[$class];
        }
        // Check if the service is defined in the container
        if ($this->has($class)) {
            $dependency = $this->definitions[$class];
            $instance = is_callable($dependency) ? $dependency($this) : $dependency;
            $this->services[$class] = $instance;

            return $instance;  
        }
    
        $reflection = new \ReflectionClass($class);
     
        if (!$constructor = $reflection->getConstructor()) {
           $instance = $reflection->newInstance();
           $this->services[$class] = $instance;
           return $instance; // If the class has no constructor, create an instance directly    
        }

        $dependencies = [];

        foreach($constructor->getParameters() as $parameter) {           
            $dependencies[] = $this->resolveParameter($parameter);
        }

        $instance = $reflection->newInstance(...$dependencies);
        $this->services[$class] = $instance;
        return $instance; // Create an instance of the class with resolved dependencies 
    }
   
    /**
     * Resolve method parameters based on URL parameters and type hints
     *
     * @param \ReflectionMethod $method
     * @param array $urlParams
     *
     * @return array Resolved parameters
     */ 
    public function resolveMethodParameters(\ReflectionMethod $method, array $urlParams): array
    {
        $parameters = [];
        $params = $method->getParameters();

        foreach ($params as $index => $param) {
            // Si on a une valeur d'URL pour ce paramètre, on l'utilise
            if (isset($urlParams[$index])) {
                $parameters[] = $urlParams[$index];
            }
            // Sinon, si le paramètre a un type (classe), on demande au container
            elseif ($param->hasType() && !$param->getType()->isBuiltin()) {
                $parameters[] = $this->get($param->getType()->getName());
            }
            // Sinon, valeur par défaut si disponible
            elseif ($param->isDefaultValueAvailable()) {
                $parameters[] = $param->getDefaultValue();
            }
            else {
                throw new \RuntimeException("Unable to resolve the parameter '{$param->getName()}' for the method {$method->getName()}");
            }
        }
        return $parameters;
    }
    /**
     * Checks if a service or definition exists in the container.
     * 
     * @param string $id The identifier of the service or definition
     * 
     * @return bool True if the service or definition exists, false otherwise
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->definitions[$id]);
    }
    /**
     * Sets a service or definition in the container.
     * 
     * @param string $id The identifier of the service or definition
     * @param mixed $definition The service definition, which can be a class name or a callable
     * 
     * @throws InvalidArgumentException If the definition is not a valid type
     */
    public function set(string $id, $definition): void
    {
        if (!is_string($definition) && !is_callable($definition)) {
            throw new \InvalidArgumentException("Definition must be a class name or callable.");
        }
        $this->definitions[$id] = $definition;
    }
    /**
     * Removes a service or definition from the container.
     * 
     * @param string $id The identifier of the service or definition to remove
     */
    public function remove(string $id): void
    {
        unset($this->services[$id], $this->definitions[$id]);
    }
    /**
     * Clears all services and definitions from the container.
     * 
     * This method resets the container to its initial state, removing all registered services and definitions.
     */
    public function clear(): void
    {
        $this->services = [];
        $this->definitions = [];
    }
    /**
     * Invokes the container as a callable to retrieve a service by its identifier.
     * 
     * @param string $id The identifier of the service
     * 
     * @return mixed The service instance
     */
    public function __invoke(string $id): mixed
    {
        return $this->get($id);
    }
    /**
     * Resolves a parameter by checking its type and whether it is defined in the container.
     * 
     * @param \ReflectionParameter $parameter The parameter to resolve
     * 
     * @return mixed The resolved value for the parameter
     * 
     * @throws CannotResolveParameterException If the parameter cannot be resolved
     */
    private function resolveParameter(\ReflectionParameter $parameter): mixed
    {   $type = $type = $parameter->getType()?->getName();
        $name = $parameter->getName();
    
        if ($type && in_array($type, self::PRIMITIVES, true)) {                 
            if ($this->has($name)) {
                return $this->get($name);                      
            } 
            if ($parameter->isOptional() && $parameter->isDefaultValueAvailable()){
                // If the parameter is optional and has a default value, use it                       
                return $parameter->getDefaultValue();                     
            }   
            throw new CannotResolveParameterException(
                sprintf('Cannot resolve parameter %s', $name)
            );                                                  
        } 
        if ($type) {
            return $this->get($type); // If the type is a class, resolve it    
        } 
        // If the type is not specified, check if the parameter name is defined
        if ($this->has($name)) {
                return $this->get($name);
        } 
        if ($parameter->isOptional() && $parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
        }
        
        throw new CannotResolveParameterException(
            sprintf('Cannot resolve parameter %s', $name)
        );
    }
}
