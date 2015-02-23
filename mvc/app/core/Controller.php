<?php
namespace Mrkvon\Ditup\Core;

use Mrkvon\Ditup\Model as Model;


class Controller
{
    function __construct()
    {
        //*** starting session and setting session variables if they don't exist  */
        session_start();
        
        //print_r($_SERVER['REQUEST_URI']);
        //echo get_class($this);
	if(get_class($this)!=='Mrkvon\Ditup\Controller\Fof'){
            $_SESSION['previous_uri'] = isset($_SESSION['previous_uri']) ? $_SESSION['previous_uri'] : '/';
            $_SESSION['current_uri'] = isset($_SESSION['current_uri'])?$_SESSION['current_uri'] : '/';
            //print_r($_SESSION);
            $_SESSION['previous_uri']=($_SERVER['REQUEST_URI']!=$_SESSION['current_uri'])?$_SESSION['current_uri']:$_SESSION['previous_uri'];
            $_SESSION['current_uri']=$_SERVER['REQUEST_URI'];
            //print_r($_SESSION);

            //print_r($_SESSION);
	}
//        $_SESSION['loggedin']=false;
        if(!isset($_SESSION['loggedin'],$_SESSION['username'], $_SESSION['from_form']) || $_SESSION['loggedin']!==true)
        {
            /****
            here checking if login cookie is present should be added
            ****/
            $cookie_login = self::staticModel('CookieLogin');
            
            $auth_usr=$cookie_login::authenticate();
            //echo 'blahblahblah';
            //print_r($auth_usr);
            if(!!$auth_usr){
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $auth_usr;
                $_SESSION['from_form'] = false;
            }
            else{
                $_SESSION['loggedin']=false;
                $_SESSION['username']='test';
                $_SESSION['from_form']=false;
            }
        }

        $this->loggedin = $_SESSION['loggedin'];
        $this->username = $_SESSION['username'];
        $this->from_form = $_SESSION['from_form'];
        
    }
    
    protected function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        $mmodel='Mrkvon\Ditup\Model\\'.$model;
        return new $mmodel();
    }

    protected static function staticModel($model)
    {
        require_once '../app/models/' . $model . '.php';
        return 'Mrkvon\\Ditup\\Model\\'.$model;
    }
    
    protected function view($view, $data = [])
    {
        /**get number of unseen messages (for top notification place)
        and generate $this->profile;
        **/
        $this->profile=[];
        if($this->loggedin===true){
            $messages_sm = self::staticModel('Messages');
            $unseen_messages = $messages_sm::countUnseenMessages($this->username);
            $this->profile['username'] = $this->username;
            $this->profile['unseen-messages'] = $unseen_messages;
        }

        require_once '../app/views/' . $view . '.php';
        exit();
    }
}
