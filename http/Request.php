<?php 

/**
 * @author  Joberne Ngambé<joberneneb@gmail.com>
 * @package Delices\Http
 */

declare(strict_types=1);

namespace Delices\Http;

class Request 
{   private const  NO_HTTP_METHOD = 'REQUEST_METHOD_NOT_FOUND';
    private const  NO_POST_DATA   = 'POST_DATA_NOT_FOUND';
    private const  NO_GET_DATA    = 'GET_DATA_NOT_FOUND';
    private const  REQUEST_METHOD_NOT_FOUND = 'NOT_HTTP_METHOD_FOUND';
    /**
     * 
     */
    public Attribute $attributes;
    /**
     * Http GET method object
     * 
     * @var GetRequest $query
     */
    public GetRequest $query;
    /**
     * Http POST method object
     */
    public PostRequest $request;
   /**
    * HTTP data array
    *
    * @var array<string,mixed>
    */
    private array $data = [];
    /**
     * Array of errors
     * 
     * @var array<string,string> $errors
     */
    private array $errors = [];
    /**
     * 
     * @return void
     */
    public function __construct(Response $response)
    {   
            $this -> attributes = new Attribute();
            
            if ($_SERVER['REQUEST_METHOD'] ===  'GET') {

                if  (!empty($_GET)) {

                $this -> query = new GetRequest();

                foreach($_GET as $key => $value) {
                    
                        $this -> query -> add($key, filter_var($value, FILTER_SANITIZE_STRING));
                    
                }

                }

                $this -> errors['HTTP'][self::NO_GET_DATA] = 'No HTTP GET data sent';
            } 

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                if (!empty($_POST)) {

                    $this -> request = new PostRequest();
                    
                    foreach($_POST as $key => $value) {

                        $this -> request -> add($key, filter_var($value, FILTER_SANITIZE_STRING));

                    }
                }

                $this -> errors['HTTP'][self::NO_POST_DATA] = 'No HTTP data posted';
            }
   }
   /**
    * Get HTPP request value by key
    *
    * @param string $key 
    * @access public
    * @return string|null
    */
   public function get(string $id): string
   {
        if ($this -> isGet() && $this -> query -> has($id)) {

            return $this -> query -> get($id);
           
        }

        if ($this -> isPost() && $this -> request -> has($id)) {

            return $this -> request -> get($id);
        }
   }
   /**
    * Check whether HTTP request_METHOD is GET
    * 
    * @return bool
    */
    public function  isGet(): bool 
    {
       return $this -> method() === 'GET' ? true : false;
    }
    /**
    * Check whether HTTP request_METHOD is POST
    * 
    * @return bool
    */
    public function isPost(): bool 
    {
        return $this -> method() === 'POST' ? true : false;
    }
   /**
    * Get HTTP request_METHOD if it exists, otherwise return NULL
    *
    * @access private
    * @return string|null
    */
    /**
     * Page redirection 
     * 
     * @param string $path Redirection path
     * @access public
     * @return void
     */
    public function header(string  $path): void
    {
        header($path);

        exit;
    }
    /**
     * Get URI
     * 
     * @access public
     * @return string
     */
    public function uri(): string
    {
        return $_SERVER['REQUEST_URI']; 
    }
    /**
     * Get current url path
     * 
     * @access public
     * @return string
     */
    public function getPathInfo(): string
    {
        return $_GET['url'] ?? '/';
    }
    /**
     * Get HTTP request method
     * 
     * @access private
     * @return string|null
     */
    private function method(): ?string
    {
        return $_SERVER['REQUEST_METHOD'] ?? NULL;
    } 
}