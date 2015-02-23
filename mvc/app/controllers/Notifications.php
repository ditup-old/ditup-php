<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Notifications extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index()
    {
        $user_sm=$this::staticModel('User');
        if($this->loggedin && $user_sm::exists($this->username)){
            exit('get static notifications from database and see dynamic notifications');
        }
    }
}
