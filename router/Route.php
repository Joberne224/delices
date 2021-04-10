<?php 

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices\Router
 */

declare(strict_types=1); 

namespace Delices\Router;

use       Delices\Container\ContainerInterface;

class Route {
	/**
	 * Regex pattern to match url
	 */
	private const PATTERN = '#{([\w]+)}#';
    /**
	 *
     * @var ContainerInterface $container
     */
    public ContainerInterface $container;
    /**
	 *
     * @var string $namespace Controller namespace
     */
    public string $namespace = '';
    /**
     * Key/value array containing named parameters
     * 
     * @var array<string,mixed> namedParams
     */
    private array $namedParams = [];
    /**
     * 
     * @var array $params
     */
    private $params = [];
    /**
     * 
     * @var array $matches
     */
    private array $matches = [];
    /**
	 *
     * @var string $path URL path
     */
    private string $path;
    /**
	 *
     * @var callable $callable Callable to run later
     */
    private $callable;
    /**
	 *
     * Constructor 
     * 
     * @param string $path
     * @param mixed  $callable
	 *
     * @return void
     */
    public function __construct(string $path, $callable) 
    {
        $this -> path = trim($path, '/');
        $this -> callable = $callable;
    }
    /**
     * Match url with the path
     * 
     * @param  string $url URL
	 *
     * @access public
	 *
     * @return bool
     */
    public function match(string $url): bool 
	{
        $url = trim($url, '/');
        $path = preg_replace_callback(self::PATTERN, [$this, 'paramMatch'], $this -> path); 
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {

            return false;

        }

        array_shift($matches); 
        
        $this -> matches = $matches;
        
        $this -> namedParams = $this -> paramNames($matches);

        return true;

    }
    /**
     * Constraint the url parameters
     * 
     * @param  string $param URL parameter
     * @param  string $regex Regular expression - defines the parameter constraint
	 *
     * @access public
	 *
     * @return self
     */
    public function with(string $param, string $regex): self 
	{
        $this -> params[$param] = str_replace('(', '(?:',$regex); 

        return $this;
    }
    /**
     * Execute the callable
     * 
     * @access public
	 *
     * @return mixed
     */
    public function call() 
    {   
        $this -> container -> set($this -> namedParams);

        $resolver  = new MethodResolver($this -> container);
    
        $resolver -> AddNamespace($this -> namespace);
       
        if (is_string($this -> callable)) {

            return $resolver -> resolve($this -> callable);
            
        } 
        
        if (is_callable($this -> callable)) {

            return call_user_func_array($this -> callable, $this -> matches);
            
        } 
        
        if (is_array($this -> callable)) {
            
            $callable = implode('#', $this -> callable);

            return $resolver -> resolve($callable, $this -> namedParams);
        }

    }
    /**
     * Add controller namespace
     * 
     * @param  string $namespace Controller namespace
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
     * Add container of injection
     * 
     * @param  ContainerInterface $container
	 *
     * @access public
	 *
     * @return self
     */
    public function addContainer(ContainerInterface $container): self
    {
        $this -> container = $container;

        return $this;
    }
    /**
     * Match parameter with the regex constraint
     * 
     * @param  array   $match
	 *
     * @access private
	 *
     * @return string 
     */
    private function paramMatch(array $match): string 
    {
        if (isset($this -> params[$match[1]])) {
            
            return '('.$this -> params[$match[1]].')';
        }

        return '([^/]+)';
    }
     /**
     * Get named parameters
     * 
     * @param  array<string,mixed> $values Array containing pairs/values
	 *
     * @access private
	 *
     * @return array<string,mixed>         Value/pair array of parameters
     */
    private function paramNames(array $values = []): array 
    {
        $keys = [];
        
        preg_match_all(self::PATTERN, $this -> path, $matches, PREG_SET_ORDER);
    
        foreach($matches as $match) {
            
            $keys[] = $match[1];

        }
       
        return array_combine($keys, $values);
    }
   
}