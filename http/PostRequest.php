<?php 

/**
 * @author  Joberne Ngambé<joberneneb@gmail.com>
 * @package Delices\Http
 */

declare(strict_types=1);

namespace Delices\Http;

use Delices\Http\Exception\IndexNotFoundException; 
use Delices\Http\Exception\IndexAlreadyFoundException;

class PostRequest {
    /**
     * @var array $request
     */
    private array $request = [];

    public function __construct(array $request = [])
    {
        $this -> request = $request;
    }
    public function add(string $id, $value)
    {
        if ($this -> has($id)) throw new IndexAlreadyFoundException(sprintf('Index %s found', $id));

        $this -> request[$id] = $value;
    }
    public function get(string $id): string 
    {
        if (!$this -> has($id)) throw new IndexNotFoundException(sprintf(
            'Index %s not found',
            $id
        ));

        return $this -> request[$id];
    }
    public function has(string $id): bool
    {
        return isset($this -> request) && array_key_exists($id, $this -> request) ?? false;
    }
    public function all(): array 
    {
        return $this -> request;
    }
}