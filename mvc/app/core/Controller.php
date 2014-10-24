<?php
namespace Mrkvon\Ditup\Core;

use Mrkvon\Ditup\Model as Model;

class Controller
{
    function __construct()
    {
        //*** starting session and setting session variables if they don't exist  */
        session_start();
        if(!isset($_SESSION['loggedin'],$_SESSION['username']))
        {
            $_SESSION['loggedin']=false;
            $_SESSION['username']='test';
        }

        $this->loggedin = $_SESSION['loggedin'];
        $this->username = $_SESSION['username'];
    }

    protected function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        $mmodel='Mrkvon\Ditup\Model\\'.$model;
        return new $mmodel();
    }
    
    protected function view($view, $data = [])
    {
        require_once '../app/views/' . $view . '.php';
    }
}
