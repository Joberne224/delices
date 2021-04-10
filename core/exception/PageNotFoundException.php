<?php 

/**
 * @author  Joberne Ngambé <joberneneb@gmail.com>
 * @package Delices\Core\Exception
 */

namespace Delices\Core\Exception;

class     PageNotFoundException extends \Exception
{
    public function __construct()
    {
        $this -> code = 404;
        $this -> message = 'Page not found. The content you are trying to access does not exist or is temporarily unavailable';
    }
}