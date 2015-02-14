<?php

namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Map extends Controller
{   
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index(){
        $this->view('general/message', [
            'loggedin' => $this->loggedin,
            'user-me' => $this->username,
            'message' => 'TODO Map. there will be map of users and projects here. it will be based on openstreetmaps & ol3.js.'
        ]);
    }
}
