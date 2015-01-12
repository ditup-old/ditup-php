<?php

function route(Array $url){
    /*** router. we have an array of possible urls
        we want to route to specific controller, which should take care of rest.

        
    
    ****/

    $controllers=[
        'change-password' => 'ChangePassword',
        'community' => 'community',
        'home' => 'home',
        'login' => 'login',
        'logout' => 'logout',
        'logout-all' => 'LogoutAll',
        'message' => 'Message',
        'messages' => 'Messages',
        'people' => 'people',
        'project' => 'project',
        'projects' => 'projects',
        'start' => 'start',
        'signup' => 'signup',
        'user' => 'user'


    ];
    $url0 = isset($url[0]) ? $url[0] : 'home';
    unset($url[0]);
    $url = $url ? array_values($url) : [];
    if(isset($controllers[$url0]) && file_exists('../app/controllers/' . $controllers[$url0] . '.php')){
        require_once '../app/controllers/' . $controllers[$url0] . '.php';
        
        $control_class = 'Mrkvon\\Ditup\\Controller\\'.$controllers[$url0];
        
        $control_class::route($url);
    }
    else{
       echo ('404 not found');
       exit();
    }

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
        
//        $this->params = $url ? array_values($url) : [];
        //print_r($this->params);
    
 //       call_user_func_array([$this->controller, $this->method],$this->params);
*/
}
