<?php

/**
 * @author  Joberne Ngambé<joberneneb@gmail.com>
 * @package Delices\Http
 */

declare(strict_types=1);

namespace Delices\Http;

use Delices\Http\Exception\IndexNotFoundException;

class Attribute {
    private array $parameters = [];

    public function add(array $params): void
    {
        if (isset($params)) {

            foreach($params as $k => $v) {

                $this -> parameters[$k] = $v;
            }
        }
    }
    public function get(string $id) {
        
        if (!$this -> has($id)) throw new IndexNotFoundException(
            sprintf('Index %s not found', $id)
        );

        return $this -> parameters[$id];

    }
    private function has(string $id): bool {

       return isset($this -> parameters) && 
       array_key_exists($id, $this -> parameters) ?? false;
    }
}