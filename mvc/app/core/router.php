<?php

function route(Array $url){
    /*** router. we have an array of possible urls
        we want to route to specific controller, which should take care of rest.

        
    
    ****/

    $controllers=[
        'change-password' => 'ChangePassword',
        'community' => 'Community',
	'dit' => 'Dit',
        'dits' => 'Dits',
        'feedback' => 'Feedback',
        'home' => 'Home',
	'idea' => 'Dit',
        'ideas' => 'Ideas',
	'interest' => 'Dit',
        'interests' => 'Interests',
	'issue' => 'Dit',
	'issues' => 'Dits',
	'topic' => 'Dit',
	'topics' => 'Dits',
        'login' => 'Login',
        'logout' => 'Logout',
        'logout-all' => 'LogoutAll',
        'map' => 'Map',
        'message' => 'Message',
        'messages' => 'Messages',
        'notifications' => 'Notifications',
        'people' => 'People',
        'project' => 'Dit',
        'projects' => 'Projects',
        'start' => 'Start',
        'signup' => 'Signup',
        'user' => 'User'
    ];

    $url0 = isset($url[0]) ? $url[0] : 'home';
    if(isset($controllers[$url0]) && file_exists('../app/controllers/' . $controllers[$url0] . '.php')){
        require_once '../app/controllers/' . $controllers[$url0] . '.php';
        
        if($controllers[$url0]!=='Dit') unset($url[0]);
        $url = $url ? array_values($url) : [];

        $control_class = 'Mrkvon\\Ditup\\Controller\\'.$controllers[$url0];

        $control_class::route($url);
        exit();
    }
    else{
        require_once '../app/controllers/404.php';
        $control_class = 'Mrkvon\Ditup\Controller\Fof';
        $control_class::route();
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
