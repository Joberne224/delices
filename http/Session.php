<?php

/**
 * @author  Joberne Ngambé<joberneneb@gmail.com>
 * @package Delices\Http
 */

declare(strict_types=1);

namespace Delices\Http;

class Session
{
    /**
     * 
     * @access public
     * @throws \Exception
     * @return void
     */
    public function start(): void
    {
        if ($this -> status()) throw new \Exception('Session has already started'); 
        
        session_start();

    }
    /**
     * Set key/value in session
     * 
     * @param string $key Session key string
     * @param mixed  $value Session key value
     * @access public 
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @return self
     */
    public function set(string $key, $value): self
    {   
        if (!$this -> has($key)) {
           
            $_SESSION[$key] = $value;

            return $this;
          
        }
        
        throw new \InvalidArgumentException(sprintf('Cannot reset key %s', $key));
    }
    /**
     * Get session key value
     * 
     * @param string $key Session key
     * @access public
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return mixed
     */
    public function get(string $key)
    {
        if ($this -> has($key)) {

            return $_SESSION[$key];

        }

        throw new \InvalidArgumentException(sprintf('%s was not set', $key));
    }
    /**
     * Unset session key/value
     * 
     * @param string $key Session key
     * @access public
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return bool
     */
    public function unset(string $key): bool
    {
        if ($this -> has($key)) {

            unset($_SESSION[$key]);

            return true;
        }

        throw new \InvalidArgumentException(sprintf('Cannot unset undeclared session key %s', $key)); 

    }
    /**
     * Destroy session
     * 
     * @throws \Exception
     * @access public
     * @return void
     */
    public function end(): void
    {
        if (!$this -> status()) throw new \Exception('Session has not started yet');

        session_destroy();
        
    }
    /**
     * Get sesion status
     * 
     * @access public
     * @throws \Exception
     * @return bool
     */
    public function has(string $key): bool 
    {
        
        if (!$this -> status()) throw new \Exception('Session has not started yet');

        return (!empty($_SESSION) && array_key_exists($key, $_SESSION)) ?? false;

    }
    /**
     * Remove all session values
     * 
     * @access public 
     * @return void
     */
    public function flush(): void 
    {
        if (!empty($_SESSION)) {

           $_SESSION = [];
        }
    }
    /**
     * Get all session values
     * 
     * @access public 
     * @return array
     */
    public function all(): array 
    {
        return $_SESSION ?? [];
    }
    /**
     * Check session status
     * 
     * @access private
     * @return bool 
     */
    private function status(): bool 
    {

       return session_status() === PHP_SESSION_ACTIVE;

    }
} 