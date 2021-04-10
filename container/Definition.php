<?php

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices\Container
 */

declare(strict_types=1);

namespace Delices\Container;

Trait     Definition 
{
    /**
     * 
     * @param  array|string $definition Add configuration 
	 *
     * @access private
     * 
     * @return self
     */
    private function add($definition): self
    {   
        if (is_array($definition)) {

            $this -> dependencies[] = $definition;

        } elseif (is_string($definition)) {

            $this -> dependencies[] = require $definition;
        }
        
        return $this;

    }
    /**
     * Retrieve configuration value for $id
	 *
     * @param  string $id
	 *
     * @access private 
     * 
     * @return mixed
     */
    private function retrieve(string $id)
    {
        $dependencies = $this -> flatten($this -> dependencies);

        return $dependencies[$id] ?? NULL;
    }
    /**
     * Flatten multidimensional 
	 *         array to a one-dimensional array
	 *
     * @param  array    $dependencies  
	 *
     * @access private 
     * 
     * @return array
     */
    public function flatten(array $dependencies): array
    {
        $result = [];

        foreach ($dependencies as $key => $value) {

            if (is_array($value)) {

                $result = array_merge($result, $this -> flatten($value));

            } else {

                $result = array_merge($result, [$key => $value]);

            }
        }

        return $result;
    }
}