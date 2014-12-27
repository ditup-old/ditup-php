<?php
namespace Mrkvon\Ditup\Core;

use Mrkvon\Ditup\Model as Model;


class Controller
{
    function __construct()
    {
        //*** starting session and setting session variables if they don't exist  */
        session_start();
        //print_r($_SESSION);
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
        require_once '../app/views/' . $view . '.php';
    }
}
