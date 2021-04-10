<?php 

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices\Router
 */

declare(strict_types=1);

namespace Delices\Router;

use       Delices\Container\ContainerInterface;
use       Delices\Router\Exception\CannotResolveParameterException;
use       Delices\Router\Exception\ControllerNotFoundException;
use       Delices\Router\Exception\MethodNotFoundException;

final class MethodResolver 
{
    private const PRIMITIVES = [
        'int',
        'float',
        'double',
        'string',
        'array'
    ];
    /**
     * Namespace 
     * 
     * @var string $namespace
     */
    private string $namespace = '';
    /**
     * Container of injection
     * 
     * @var Container $container
     */
    private ContainerInterface $container;
    /**
     * Inject container dependency 
     * 
     * @param  ContainerInterface $container
	 *
     * @access public 
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this -> container = $container;
    }
     /**
     * Instantiate an object and  call its corresponding method by passing its arguments
     * 
     * @param  string                          $callback Callback string
     * 
     * @access public
     * 
     * @throws ControllerNotFoundException
     * @throws MethodNotFoundException
     * @throws CannotResolveParameterException
     * 
     * @return mixed
     */
    public function resolve(string $callback) 
    {
        // Parse callback string into controller and action names
        $this -> parse($callback);
    
        try {
            // Inspect controller class by using reflection tools
            $classReflection = new \ReflectionClass($this -> callbackClass);
           
            try {
                // Get action reflection class 
                $methodReflection =  $classReflection -> getMethod($this -> callbackMethod);
                /**
                 * @var array $dependencies Array of action argument dependencies 
                 */
                $dependencies = [];
                // Loop through action parameters 
                foreach($methodReflection -> getParameters() as $methodParam) {
                    // Check if parameter is type-hinted - step 1
                    if ($methodParam -> hasType()) {
                        // Get type name - step 2
                        $type = $methodParam -> getType() -> getName();
                        // Check if the type is in array of primitives - step 3
                        if (in_array($type, self::PRIMITIVES)) {
                            // get parameter name - step 4
                            $name = $methodParam -> getName();
                            // check if the given parameter is in array of parameters passed to the resolver - step 5
                            if ($this -> container -> has($name)) {

                                array_push($dependencies, $this -> container -> get($name)); 
                           
                            } else {
                                // check if the parameter is optional and has a default value set - step 6
                                if ($methodParam -> isOptional() && $methodParam -> isDefaultValueAvailable()) {
                                    // Grab the the default value and push it onto the dependencies list - step 7
                                    array_push($dependencies, $methodParam -> getDefaultValue());
                                    
                                    continue;

                                }
                                // Throw an exception if no optional and default value is found - step 8
                                throw new CannotResolveParameterException(
                                    sprintf('Cannot resolve parameter %s', $name)
                                );
                            
                            }
                        
                        } else {

                            // Recursively get dependencies of the parameter type if it's an object - step 9
                            array_push($dependencies, $this -> container -> get($type));

                        }

                    } else {
                        // Parameter has no type hint
                        // Get its name 
                        $name = $methodParam -> getName();
                        // Same as step 5
                        if ($this -> container -> has($name)) {

                            array_push($dependencies, $this -> container -> get($name));

                        } else {
                            // Same as step 6
                            if ($methodParam -> isOptional()) {
                                // Check if unhinted parameter is not a built-in object and get default value - 10
                                if (!$methodParam -> getDeclaringClass() -> isInternal()) {
                                    
                                    array_push($dependencies, $methodParam -> getDefaultValue());
                                
                                    continue;

                                }

                                continue;
                            } 
                            // Throw an exception if the unhinted parameter has no default value
                            throw new CannotResolveParameterException(
                                sprintf('Cannot resolve parameter %s', $name)
                            );
                            
                        }
                    }
                }
                // Initialize the declaring controller by using dependency container
                $initClass = $this -> container -> get($this -> callbackClass); 
                // Return the invoked action using reflection 
                return $methodReflection -> invoke($initClass, ...$dependencies);

            } catch(\ReflectionException $e) {
                // Throw an exception if the invoked method is not found
                throw new MethodNotFoundException(
                    sprintf('Method %s::%s() not found', $this -> callbackClass, $this -> callbackMethod)
                );
            }       

        } catch(\ReflectionException $e) {
            // Throw an exception if the controller is not found
            throw new ControllerNotFoundException(
                sprintf('Class %s not found', $this -> callbackClass)
            );
        }  
       
    }
    /**
     * 
     * @param  string $namespace
	 *
     * @access public
     * 
     * @return self
     */
    public function AddNamespace(string $namespace): self
    {
        $this -> namespace = $namespace;

        return $this;
    }
    /**
     * Resolve callback string
     * 
     * @param  string $callback
	 *
     * @access private
     * 
     * @return void
     */
    private function parse(string $callback): void 
    {
        $segments = explode('#', $callback);
        $this -> callbackClass = $this -> namespace.ucfirst($segments[0]);
        // If action name is set then set controller action name else call __invoke()
        $this -> callbackMethod = isset($segments[1]) ? $segments[1] : '__invoke';
    }
 }