<?php 

/**
 * @author Joberne Ngambé<joberneneb@gmail.com>
 * @package Delices\Http
 */

declare(strict_types=1);

namespace Delices\Http;

class Response {
    /**
     * @var string $content
     */
    private string $content;
    /**
     * @var int $code
     */
    private int $code;
    /**
     * 
     */
    public function __construct(string $content = '', int $code = 0) 
    {
        $this -> content = $content;
        $this -> code    = $code;
    }
    /**
     * Set response content
     * 
     * @param string $content Response content
     * @access public
     * @return void
     */
    public function setContent(string $content): void 
    {
        echo htmlspecialchars($content, ENT_QUOTES);
    }
    /**
     * Set Http status code
     * 
     * @param int $code 
     * @access public 
     * @return void
     */
    public function setStatusCode(int $code): void 
    {
        http_response_code($code);
    }
    /**
     * Redirect to the url
     * 
     * @param string $url
     * @access public
     * @return void
     */
    public function redirect(string $url): void
    {
        header("Location: {$url}");
    }
    /**
     * 
     */
    public function send() 
    {

    }
    /**
     * Set response headers
     * 
     * @param string $str Header string
     * @access public 
     * @return void
     */
    public function header(string $str): void 
    {
        header($str);
    }
 }