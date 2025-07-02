<?php
declare(strict_types=1);

namespace App\Core\Routing;
use App\Container\ContainerInterface;

class Route
{
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
        $this->path = trim($path, '/');
        $this->callable = $callable;
    }
    /**
     * Set the parameters for the route
     * @param string $url
     * 
     * @return void
     */
    public function match(string $url): bool 
	{
        $url = trim($url, '/');
        $path = preg_replace_callback(self::PATTERN, [$this, 'paramMatch'], $this->path); 
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }
        array_shift($matches); 
        $this->matches = $matches;
        $this->namedParams = $this->paramNames($matches);
        return true;
    }
    /**
     * Excute the callable with the matched parameters
     * 
     * @return array<string,mixed>
     */
    public function call() 
    {   
        if (is_callable($this->callable)) {
            return call_user_func_array($this->callable, $this->matches);
        }  
        if (is_string($this->callable)) {
            $parts = explode('#', $this->callable);
            $controller = $this->namespace.'\\'.ucfirst($parts[0]);
            $methode = $parts[1];
            
            if (!class_exists($controller)) {
                throw new \RuntimeException("Controller class $controller does not exist.");
            }
            $controllerReflection = new \ReflectionClass($controller);
            $instance = isset($this->container) ? $this->container->get($controller) : $controllerReflection->newInstance();
            $methodeReflection = $controllerReflection->getMethod($methode);
            $args = isset($this->container) 
            ? $this->container->resolveMethodParameters($methodeReflection, $this->matches) : [];

            echo $methodeReflection->invokeArgs($instance, $args);
        }
    }
    /**
     * Set the namespace for the controller
     * @param string $namespace
     * 
     * @return void
     */
    public function setNamespace(string $namespace): void 
    {
        $this->namespace = $namespace;
    }
    /**
     * Set the container for dependency injection
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container): void 
    {
        $this->container = $container;
    }
     /**
     * Get the named parameters
     * @param array $values Values to set for the named parameters
     * 
     * @return array<string,mixed>
     */
    private function paramNames(array $values = []): array 
    {
        $keys = [];
        preg_match_all(self::PATTERN, $this->path, $matches, PREG_SET_ORDER);
    
        foreach($matches as $match) {  
            $keys[] = $match[1];
        } 
        return array_combine($keys, $values);
    }
    /**
     * Get the named parameters
     * @param array $match Match array from preg_match
     * 
     * @return string 
     */
    private function paramMatch(array $match): string 
    {
        if (isset($this->params[$match[1]])) {
            return '('.$this->params[$match[1]].')';
        }
        return '([^/]+)';
    }
}
