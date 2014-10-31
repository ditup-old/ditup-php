<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class People extends Controller
{
    public function index($action='')
    {
            $this->view('people/index', ['loggedin' => $this->loggedin, 'user' => $this->username]);
    }
}
