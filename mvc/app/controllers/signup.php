<?php

class Signup extends Controller
{   
    public function index()
    {
        if($this->loggedin){
            //***sign up works only if person is not logged in*/
            $this->view('signup/log-out-to-sign-up');
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
                    echo 'all ok. entering data to database.';
                    $user->setUsername($username);
                    $user->create(['email' => $email, 'username' => $username, 'password' => $password]);
                    $user->sendVerificationEmail();
                    unset($user);
                }
                else {
                    /***
                    possible errors:
                    1:  email not valid
                    2:  username not valid (a-z,0-9,.,-,_)
                    4:  username already used
                    8:  password not strong enough
                    16: passwords don't match
                    32: antibot protection didn't pass
                    */
                    $this->view('signup/index', ['errors'=>$errors, 'values'=>['email' => $email, 'username' => $username]]);
                }

            }
            else {
                $this->view('signup/index');
            }
        }
    }

    private function bot(){
        return false;
    }
    
}
