<?php

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices\Core
 */

declare(strict_types=1); 

namespace Delices\Core;

class     Controller 
{
    /**
     * View object
     * 
     * @var View $view
     */
    protected View $view;
    /**
     * Get model instance 
     * 
     * @param  string                     $model Fully qualified model class 
     *
	 * @access protected
	 *
     * @throws \InvalidArgumentException
	 *
     * @return mixed
     */
    final protected function model(string $model) 
    {
        if ($model === '')  throw new \Exception('Model namespace cannot be empty');

        $segments  = explode('\\', $model); 
        $className = array_pop($segments);

        array_shift($segments);

        $file = dirname(__DIR__).'/'.strtolower(implode('/', $segments)).'/'.$className.'.php';

        if (file_exists($file)) {

            require_once $file; 

            return new $model();
        }

        throw new \InvalidArgumentException(
            sprintf('Invalid fully qualified model class', $model)
        );
    }
    /**
     * Instantiate view object
     * 
     * @param  string     $file view path
     * @param  array      $data data to pass to the view
	 *
     * @access protected
	 *
     * @return View
     */
    final protected function view(string $file, array $data = []): View 
    {
        $this -> view = new View($file, $data); 

        return $this -> view;
    }
   
}