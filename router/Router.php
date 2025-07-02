<?php
declare(strict_types=1);

namespace App\Core\Routing;
use App\Core\Routing\Exceptions\RouteNotFoundException;
use App\Core\Routing\Exceptions\RequestMethodNotFoundException;
use App\Container\Container;
use App\Core\Routing\Route;
/** Router class for handling HTTP requests and routing them to the appropriate controllers and methods.
 * 
 * This class allows you to define routes for GET and POST requests, and it will match the current URL
 * to the defined routes, calling the appropriate method when a match is found.
 */
class Router 
{   
    /**
    * @var string $url The URL to route.
    */
    private string $url;
    /**
    * @var \App\Container\Container|null $container The container for dependency injection.
    */
    private ?\App\Container\Container $container;

    private array $routes = [];
    /**
     * Constructor for the Router class.
     *
     * @param string $url The URL to route.
     * @param \App\Container\Container|null $container The container for dependency injection.
     */
    public function __construct(
        string $url,
        ?\App\Container\Container $container = null
    ) 
    {
        $this->url = $url;
        $this->container = $container;
    }
    /**
     * Get the URL to route.
     * 
     * @param string $path
     * @param callable|string $callable 
     *
     * @return string The URL to route.
     */
    public function get(string $path, callable|string $callable): Route 
	{
        $route = new Route($path, $callable); 
        if ($this->container) {
            $route->setContainer($this->container);
        }   
        $this->routes['GET'][$path] = $route;
        return $route;
    }
    /**
     * 
     * 
     * @param string $path
     * @param callable|string $callable 
     *
     * @return string The URL to route.
     */
    public function post(string $path, $callable): Route 
	{
        $route = new Route($path, $callable); 
        if ($this->container) {
            $route->setContainer($this->container);
        }   
        $this->routes['POST'][$path] = $route;
        return $route;
    }
    /**
     * Run the router to match the current URL with defined routes.
     *
     * @throws \App\Core\Routing\Exceptions\RouteNotFoundException If no route matches the current URL.
     * @throws \App\Core\Routing\Exceptions\RequestMethodNotFoundException If the request method is not set.
     *
     * @return mixed The result of the matched route's call.
     */
    public function run() 
	{
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new RequestMethodNotFoundException('REQUEST_METHOD does not exist');
        }
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call(); 
            }
        }
        throw new RouteNotFoundException('No route matches.');
    }
}
