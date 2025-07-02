<?php
declare(strict_types=1);    
namespace App\Container;
/**
 * Interface for auto-wiring dependencies in a container.
 *
 * This interface defines the method required for resolving method parameters
 * using reflection and matches from a route.
 */
interface AutoWiringInterface
{   
    /**
     * Resolve method parameters using reflection and matches.
     * @param \ReflectionMethod $method The method to resolve parameters for.
     * @param array $matches An array of matches from a route.
     * @return array An array of resolved parameters.
     * @throws \App\Container\Exceptions\CannotResolveParameterException
     */
    public function resolveMethodParameters(\ReflectionMethod $method, array $matches): array;
}
