<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class People extends Controller
{
    public function index($name = '', $action='')
    {
        $user = $this->model('User');
        $user->name = $this->username;
        $member = $this->model('User');
        $member->name = $name;
        
        if($name===''){
            $this->view('people/index', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
        }
        elseif($member->exists($name)){
            //get profile data from database
            
            if($member->name === $user->name && $this->loggedin){
                if($action === 'edit'){
                    $this->view('people/profile-edit', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
                }
                else{
                    $this->view('people/profile-me', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
                }
            }
            else{
                $this->view('people/profile', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
            }
        }
        else{
            $this->view('people/not-found', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name]);
        }
    }
}
