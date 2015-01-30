<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class People extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index($action='')
    {
        $this->view('people/index', ['loggedin' => $this->loggedin, 'user' => $this->username]);
    }
}
