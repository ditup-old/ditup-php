<?php

class Controller
{
    function __construct()
    {
        //*** starting session and setting session variables if they don't exist  */
        session_start();
        if(!isset($_SESSION['loggedin'],$_SESSION['user']))
        {
            $_SESSION['loggedin']=false;
            $_SESSION['user']='test';
        }

        $this->loggedin=$_SESSION['loggedin'];
        $this->user=$_SESSION['user'];
    }

    protected function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }
    
    protected function view($view, $data = [])
    {
        require_once '../app/views/' . $view . '.php';
    }
}
