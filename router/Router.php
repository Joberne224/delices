<?php 

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices\Router
 */

declare(strict_types=1);

namespace Delices\Router;

use       Delices\Container\ContainerInterface;
use       Delices\Router\Exception\RequestMethodNotFoundException;
use       Delices\Router\Exception\NoRouteFoundException;

class      Router 
{
    /**
	 *
     * @var Container\ContainerInterface $container
     */
    private ContainerInterface $container;
    /**
	 *
     * @var string $url URL
     */
    private string $url;
    /**
	 *
     * @var Route[] $routes
     */
    private array $routes = [];
    /**
     * 
     * @param string $url
	 *
     * @return void
     */
    public function __construct(string $url) 
    {
        $this -> url = $url;
    }
    /**
	 * 
     * @param  $path URL path
     * @param  mixed Callable to run later
	 *
     * @return Route
     */
    public function get(string $path, $callable): Route 
	{

        $route = new Route($path, $callable); 

        $this -> routes['GET'][$path] = $route;

        return $route;
    }
    /**
	 *
     * @param string   $path URL path
     * @param callable       Callable to run later
	 * 
     * @return void
     */
    public function post(string $path, $callable): Route 
	{

        $route = new Route($path, $callable); 

        $this -> routes['POST'][$path] = $route;

        return $route;
    }
    /**
     * Run - executes HTTP requests
     * 
     * @throws RequestMethodNotFoundException
	 * @throws NoRouteFoundException
	 *
     * @return mixed
     */
    public function run() 
	{
        
        if (!isset($_SERVER['REQUEST_METHOD'])) {

            throw new RequestMethodNotFoundException('REQUEST_METHOD does not exist');

        }

        foreach($this -> routes[$_SERVER['REQUEST_METHOD']] as $route) {

            if ($route -> match($this -> url)) {

                return $route -> call();
                
            }
        }

        throw new NoRouteFoundException('No route matches.');
    }
}