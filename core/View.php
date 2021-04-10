<?php

/**
 * @author  Bedel Ngambé Ebouato <joberneneb@gmail.com>
 * @package Delices/Core
 */

declare(strict_types=1); 

namespace Delices\Core;

use       Delices\Core\Exception\PageNotFoundException;

class      View 
{   
    /**
     * Page title 
     * 
     * @var string $title
     */
    private string $title;
    /**
     * View directory path
     * 
     * @var string $path View path
     */
    private string $path = '';
    /**
     * File path
     * 
     * @var string $file
     */
    private string $file = ''; 
    /**
     * Data to pass to the view
     * 
     * @var array $data 
     */
    private array $data = [];
    /**
     * 
     * @param  string $file  File path
     * @param  array  $data  Data to pass to the view
	 *
     * @access public
	 *
     * @return void
     */
    public function __construct(string $file, array $data = []) {

        $this -> file = $file;
        $this -> data = $data; 
    }
    /**
     * Render the view
     * 
     * @access public
	 *
     * @throws PageNotFoundException
	 *
     * @return bool
     */
    public function render(): bool 
    {
        $file = $this -> path.$this -> file.'.phtml';
       
        if (file_exists($file)) {

            $view   = $this -> view($file, $this -> data);
            $layout = $this -> layout('layout.phtml');
            $content = str_replace('{{content}}', $view, $layout);

            echo $content;
            
            return true;

        }   

        throw new PageNotFoundException();
    }
    /**
     * Set view directory path
     * 
     * @param  string $path
	 *
     * @access public
	 *
     * @return self
     */
    public function path(string $path): self
    {
        $this -> path = $path;

        return $this;
    }
    /**
     * Set page title
     * 
     * @param  string $title Page title
	 *
     * @access public
	 *
     * @return self
     */
    public function title(string $title): self
    {
        $this -> title = $title;

        return $this;
    } 
    /**
     * Get view content by filename and passing data
     * 
     * @param  string  $view  View filename
     * @param  array   $data  Data passed to the view
	 *
     * @access private
	 *
     * @return string  Output buffer
     */
    private function view(string $view, array $data = []): string
    {   
        extract($data, EXTR_SKIP);

        ob_start();
        
        include_once $view;

        return ob_get_clean();
    }
    /**
     * Get layout for the View
     * 
     * @param  string  $layout Layout path
	 *
     * @access private
	 *
     * @return string  Output buffer
     */
    private function layout(string $layout): string 
    {   // Page title
        $title = $this -> title ?? 'Delices Framework';

        ob_start(); 

        include_once $this -> path.$layout;

        return ob_get_clean();

    }
}