<?php

namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Login extends Controller
{   
    public function index()
    {
        if($this->loggedin){
            //***sign up works only if person is not logged in*/
            $this->view(
                'general/error',
                [
                    'loggedin' => $this -> loggedin,
                    'user-me' => $this -> username,
                    'message' => 'You are already logged in as ' . $this->username . '. You may want to <a href="/logout">log out</a> to log in as different user.'
                ]
            );
        }
        else {
            if(isset($_POST, $_POST['username'], $_POST['password'])){
                //print_r($_POST);
                $username = $_POST['username'];
                $password = $_POST['password'];
                $persistent = (isset($_POST['persistent']) && $_POST['persistent'] == true) ? true : false;
                
                $errors=[];
                
                $user=$this->model('User');

                if($user->login(['username' => $username, 'password' => $password], $errors)){
                    $_SESSION['username'] = $username;
                    $_SESSION['loggedin'] = true;
                    $_SESSION['from_form'] = true;

                    if($persistent){
                        $cookie_login = self::staticModel('CookieLogin');
                        $cookie_login::cleanLoginCookies();
                        $cookie_login::createLoginCookie(['username' => $username]);
                    }
                    //echo 'successfuly logged in (finish this process!)';
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
