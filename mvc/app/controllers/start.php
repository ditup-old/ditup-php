<?php

namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Start extends Controller
{
    public function index($name = '')
    {
//        $user = $this->model('User');
//        $user->name = $name;

        $this->view('start/index');
    }
}
