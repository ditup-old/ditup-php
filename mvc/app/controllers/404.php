<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Fof extends Controller
{
    public static function route(){
        $self = new self;
        $self->index();
    }
    
    private function index(){
        $this->view('general/404', [
            'loggedin' => $this->loggedin,
            'user-me' => $this->username
        ]);
    }
}
