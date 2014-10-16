<?php

class Home extends Controller
{
    public function index()
    {
        $user = $this->model('User');
        $user->name = $this->user;

        $this->view('home/index', ['loggedin' => $this->loggedin, 'name' => $user->name]);
    }
}
