<?php 

/**
 * @author  Joberne Ngambé <joberneneb@gmail.com>
 * @package Delices\Container
 *
 */

declare(strict_types=1);

namespace Delices\Container; 

use       Delices\Container\Exception\ServiceNotFoundException;
use       Delices\Container\Exception\CannotResolveParameterException;

final class Container implements ContainerInterface 
{   
    use Definition;
   
    private const PRIMITIVES = [
        'int',
        'float',
        'double',
        'string',
        'array'
    ];
    /**
     * 
     * @var array<string,mixed> $dependencies
     */
    private array  $dependencies = [];
    /**
     * Get the class instance automatically
	 * or configuration value
     * 
     * @param string   $class Fully qualified class name
     *                        or configuration id
     * 
     * @access public
     * 
     * @return mixed   Return an object or a primitive type
     */
    public function get(string $class)
    {  
        if ($this -> has($class)) {
        
            $dependency = $this -> retrieve($class);

            if (is_callable($dependency)) return $dependency($this);

            return $dependency;
        }
    
        $classReflection = new \ReflectionClass($class);
     
        if (!$constructor = $classReflection -> getConstructor()) return $classReflection -> newInstance();

        $dependencies = [];

        foreach($constructor -> getParameters() as $constructorParam) {
           
            if ($constructorParam -> hasType()) {

                $type = $constructorParam -> getType() -> getName();

                if (in_array($type, self::PRIMITIVES)) {
                   
                    $name = $constructorParam -> getName();
  
                    if ($this -> has($name)) {
                      
                        array_push($dependencies, $this -> get($name));
                       
                    } else {
                       
                        if ($constructorParam -> isOptional() && $constructorParam -> isDefaultValueAvailable()) {
                         
                            array_push($dependencies, $constructorParam -> getDefaultValue());

                            continue;
                            
                        }  

                        throw new CannotResolveParameterException(
                            sprintf('Cannot resolve parameter %s', $name)
                        );
                        
                    }

                } else {
    
                    array_push($dependencies, $this -> get($type));
                    
                }

            } else {

                $name = $constructorParam -> getName();

                if ($this -> has($name)) {

                    array_push($dependencies, $this -> get($name));

                } else {

                    if ($constructorParam -> isOptional()) {

                        if (!$constructorParam -> getDeclaringClass() -> isInternal()) {
                         
                            array_push($dependencies, $constructorParam -> getDefaultValue());

                        }
   
                    } else {
                        
                        throw new CannotResolveParameterException(
                            sprintf('Cannot resolve parameter %s', $name)
                        );
                    }

                }
            }
        }

        return $classReflection -> newInstance(...$dependencies);
    }
	/**
	 * Check if an id is set in the container
	 * 
	 * @param string $id
	 *
	 * @access public
	 * 
	 * @return bool
	 */
    public function has(string $id): bool 
    {   
        return array_key_exists($id, $this -> dependencies) ||
              array_key_exists($id, $this -> flatten($this -> dependencies)) ?? false;
    }
    /**
     * Add definitions
     * 
     * @param  string $dependencies
     * @access public
     * 
     * @return self
     */
    public function set($dependencies): self
    {  
        return $this -> add($dependencies);

    }
    /**
     * Unset id from container
     * 
     * @param string   $id Object id
	 *
     * @access public
	 *
     * @throws ServiceNotFoundException
     * 
     * @return bool
     */
    public function remove(string $id): bool {
        
        if (!$this -> has($id)) throw new ServiceNotFoundException(
            sprintf('Service not found:  %s', $id)
        );
        
		$this -> dependencies = $this -> flatten($this -> dependencies); 
		
        unset($this -> dependencies[$id]);

        return true;
    }
}
