<?php

namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Login extends Controller
{   
    public function index()
    {
        if($this->loggedin){
            //***sign up works only if person is not logged in*/
            $this->view('logout');
        }
        else {
            if(isset($_POST, $_POST['username'], $_POST['password'])){
                $username = $_POST['username'];
                $password = $_POST['password'];
                
                $errors=[];
                
                $user=$this->model('User');

                if($user->login(['username' => $username, 'password' => $password], $errors)){
                    $_SESSION['username'] = $username;
                    $_SESSION['loggedin'] = true;
                    echo 'successfuly logged in (finish this process!)';
                    header('Location:/');
                }
                else{
                    $this->view('login/index', ['errors'=>$errors, 'values'=>['username' => $username]]);
                }
                unset($user);
                    

            }
            else {
                $this->view('login/index');
            }
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
