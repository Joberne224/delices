<?php
declare(strict_types=1);
namespace App\Container;
/**
 * Interface for a service container.
 *
 * This interface defines the methods required for a service container
 * that can manage dependencies and provide services.
 */
interface ContainerInterface extends AutoWiringInterface
{
    /**
     * Get a service from the container.
     *
     * @param string $id The service identifier.
     * @return mixed The service instance.
     */
    public function get(string $id): mixed;

    /**
     * Check if a service exists in the container.
     *
     * @param string $id The service identifier.
     * @return bool True if the service exists, false otherwise.
     */
    public function has(string $id): bool;
}
