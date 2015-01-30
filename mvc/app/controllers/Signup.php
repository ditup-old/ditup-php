<?php

namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Signup extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index()
    {
        if($this->loggedin){
            //***sign up works only if person is not logged in*/
            $this->view('general/error', [
               'loggedin' => $this->loggedin,
               'user-me' => $this->username,
               'message' => 'first <a href="/logout">log out</a> to sign up'
            ]);
            exit();
        }
        else {
            if(isset($_POST, $_POST['email'], $_POST['username'], $_POST['password'], $_POST['password2'], $_POST['full-name'])){
                $email = $_POST['email'];
                $username = $_POST['username'];
                $password = $_POST['password'];
                $password2 = $_POST['password2'];
                $full_name = $_POST['full-name']; //spambot protection; should remain empty
                //*** validate email, username, password, check password match and antibot protection*/
                
                $errors=[];
                
                $user=$this->model('User');

                if(!$this->bot() && $user->validate(['email' => $email, 'username' => $username, 'password' => $password, 'password2' => $password2], $errors)){
                    //*** enter user to database and send verification link */
                    //echo 'all ok. entering data to database.';
                    $user->setUsername($username);
                    $user->create(['email' => $email, 'username' => $username, 'password' => $password]);
                    $user->sendVerificationEmail();
                    unset($user);
                    $this->view('general/message', [
                        'loggedin' => $this->loggedin,
                        'user-me' => $this->username,
                        'message' => $username.', you were successfully signed up. now please check your email inbox and follow verification instructions to finish the process. email verification prevents other entities to misuse your email address on this platform.'
                    ]);
                    exit();
                }
                else {
                    /***
                    possible errors:
                    1:  email not valid
                        email already used
                    2:  username not valid (a-z,0-9,.,-,_)
                    4:  username already used
                    8:  password not strong enough
                    16: passwords don't match
                    32: antibot protection didn't pass
                    */
                    $this->view('signup/index', ['errors'=>$errors, 'values'=>['email' => $email, 'username' => $username]]);
                    exit();
                }

            }
            else {
                $this->view('signup/index');
                exit();
            }
        }
    }

    private function bot(){
        return false;
    }

    public function verify($username, $verify_code){
        $user=$this->model('User');
        if($user->verify(['username' => $username, 'verify_code' => $verify_code])){
            $this->view('general/message', [
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'message' => 'verification was successful. now you can <a href="/login">log in</a>'
            ]);
        }
        else{
            $this->view('general/error', [
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'message' => 'verification was not successful. possible reasons: verification code incorrect, verification code expired, user already verified. IMPLEMENT resending verification link'
            ]);
        }
    }
}
