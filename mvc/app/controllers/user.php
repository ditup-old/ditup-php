<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class User extends Controller
{
    public function index($name = '', $action='')
    {
        $user = $this->model('User');
        $user->name = $this->username;
        $member = $this->model('User');
        $member->name = $name;
        
        if($name===''){
            /****redirect to general people page*****/
            header('Location:/people');
            exit();
        }
        elseif($member->exists($name)){
            //get profile data from database
            
            switch($action){
                case 'edit':
                    if($member->name === $user->name && $this->loggedin){
                        $this->view('people/profile-edit', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
                    }
                    else {
                        echo 'you don\'t have rights to edit this profile'; 
                    }
                    break;
                case 'info':
                    
                    break;
                default:
                    if($member->name === $user->name && $this->loggedin){
                        $this->view('people/profile-me', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
                    }
                    else{
                        $this->view('people/profile', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);       
                    }
            }
        }
        else{
            $this->view('people/not-found', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
        }
    }
}
