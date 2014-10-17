<?php

class Home extends Controller
{
    public function index()
    {
        $user = $this->model('User');
        $user->name = $this->username;

        $this->view('home/index', ['loggedin' => $this->loggedin, 'name' => $user->name]);
    }
}
