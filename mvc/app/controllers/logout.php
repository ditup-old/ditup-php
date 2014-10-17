<?php

class Logout extends Controller
{   
    public function index()
    {
        if($this->loggedin){
            session_destroy();
        }
        else {
            echo 'no need to log out. you are not logged in.';
        }
    }

    private function bot(){
        return false;
    }

    public function verify($username, $verify_code){
        $user=$this->model('User');
        $user->verify(['username' => $username, 'verify_code' => $verify_code]);
    }
    
}
