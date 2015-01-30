<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Logout extends Controller
{   
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index()
    {
        $redirect_uri = isset($_SESSION['previous_uri'])?$_SESSION['previous_uri']:'/';
        if($this->loggedin){
            //if user is logged in, we will log him out
            session_destroy();
            //deleting login cookies
            $cookie_login = $this->staticModel('CookieLogin');
            $cookie_login::destroyLoginCookie();

            echo 'successfuly logged out';
            header('Location:'.$redirect_uri); //improve!!
            exit();
        }
        else {
            echo 'no need to log out. you are not logged in.';
            header('Location:'.$redirect_uri); //improve!!
            exit();
        }
    }
}
