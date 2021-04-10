<?php 

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices\Container
 */
 
declare(strict_types=1);

namespace Delices\Container;

interface ContainerInterface
{
	/**
	 * Set dependencies
	 * 
	 * @param  array|string $dependencies Dependencies - array of dependencies 
	 *                                    or configuration file path.
	 *                                    Use to resolve interface types or primitives.
	 *
	 * @return $this;
	 */
	public function set($dependencies): self;
	public function get(string $id); 
	public function has(string $id): bool;
	public function remove(string $id): bool;
}