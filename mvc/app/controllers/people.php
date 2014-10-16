<?php

class People extends Controller
{
    public function index($name = '')
    {
        $user = $this->model('User');
        $user->name = $this->user;
        $member = $this->model('User');
        $member->name = $name;

        $this->view('people/index', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
    }
}
