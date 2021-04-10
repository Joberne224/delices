<?php

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices\Core
 */

declare(strict_types=1);

namespace Zara\Core;

abstract   class FormModel 
{   
    /**
     * @var array $data
     */
    protected array $data = [];
    /**
     * Array of errors
     * 
     * @var array $errors
     */
    private array $errors = [];
    /**
     * Add error message 
     * 
     * @param string $attribute Model attribute
	 *
     * @access public
	 *
     * @return void
     */
    public function addError(string $attribute, string $message): void
    {
        if ($this -> has($attribute)) {

            throw new Exception(
                sprintf('Cannot reset %s attribute', $attribute)
            );
        }

        $this -> errors[$attribute] = $message;
    }
    /**
     * Get error message for a given model attribute
     * 
     * @param string $attribute Model attribute
	 *
     * @access public 
	 *
     * @return string|null
     */
    public function error(string $attribute): ?string 
    {
        return $this -> has($attribute) ? $this -> errors[$attribute] : NULL;
    }
    /**
     * Get all errors as an array
     * 
     * @access public 
	 *
     * @return array<string,mixed>
     */
    public function errors(): array 
    {
        return $this -> errors;
    }
    /**
     * Load form data
     * 
     * @param  array<string,mixed> $data Form data
     * @access public
	 *
     * @return void
     */
    abstract public function load(array $data): void;
    /**
     * Validate form data
     * 
     * @access public
	 *
     * @return bool
     */
    abstract public function validate(): bool;
    /**
     * Check if a given attribute message is set
     * 
     * @param  string     $attribute Model attribute
	 *
     * @access protected
	 *
     * @return bool
     */
    protected function has(string $attribute): bool
    {
        return isset($this -> errors[$attribute]);
    }
}