<?php

namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Start extends Controller
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

    public function index($name = '')
    {
//        $user = $this->model('User');
//        $user->name = $name;

        $this->view('start/index');
    }
}
