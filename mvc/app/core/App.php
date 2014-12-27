<?php
namespace Mrkvon\Ditup\Core;

use Mrkvon\Ditup\Controller as Controller;

class App
{
    
    //protected $controller = 'home';

    //protected $method = 'index';

    //protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();
        
        require_once '../app/core/router.php';

        route($url);

/*        if(file_exists('../app/controllers/' . $url[0] . '.php'))
        {
            $this->controller = $url[0];
            unset($url[0]);
        }

        require_once '../app/controllers/' . $this->controller . '.php';

        $this->controller = str_replace(['-'], '', $this->controller);
        
        $this->controller = 'Mrkvon\\Ditup\\Controller\\'.$this->controller;
        
        $this->controller = new $this->controller;
        
        if(isset($url[1]))
        {
            if(method_exists($this->controller, $url[1]))
            {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        $this->params = $url ? array_values($url) : [];
        //print_r($this->params);
    
        call_user_func_array([$this->controller, $this->method],$this->params);
        */
    }

    public function parseUrl()
    {
        if(isset($_SERVER['REQUEST_URI'])) {
            $url = filter_var(trim($_SERVER['REQUEST_URI'], '/'), FILTER_SANITIZE_URL);
            return strlen($url) !== 0 ? explode('/',$url) : [];
        }
    }
}
