<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;


class Home extends Controller
{   
    public static function route($url){
        $self = new self;
        if(isset($url[0])){
            $self->$url[0]();
        }
        else{
            $self->index();
        }
    }

    public function index()
    {   
        if(empty($_COOKIE['returning'])) 
        {
            setcookie('returning', true, time()+60*60*24*28); // Expires in a month
            header('Location: /start');
            exit();
        }
        $user = $this->model('User');
        $user->name = $this->username;

        $this->view('home/index', ['loggedin' => $this->loggedin, 'user-me' => $user->name]);
    }
}
