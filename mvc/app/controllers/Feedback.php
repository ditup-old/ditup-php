<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Feedback extends Controller
{
    public static function route(){
        $self = new self;
        $self->index();
    }
    
    private function index(){
        $this->view('feedback/index', [
            'loggedin' => $this->loggedin,
            'user-me' => $this->username
        ]);
    }
}
