<?php
use Mrkvon\Ditup\Model\Database as Database;

require_once dirname(__FILE__).'/database/UserAccounts.php';

class User
{
    private $username;
    private $verify_code;
    private $delete_code;
    private $email;
    
    public function setUsername($username){
        $this->username=$username;
    }
    
    private function exists($username){
//        echo dirname(__FILE__);
//        require_once dirname(__FILE__).'/database/UserAccounts.php';
        //$dbusr = new Database\UserAccounts();
        $data = Database\UserAccounts::selectAccountByUsername($username);
        //unset($dbusr);
        return sizeof($data) > 0 ? true : false;
    }

/** this function creates new user in database and sets*********/

    public function create(Array $values){
//        $this->delete_code = $delete_code = $this::createHexCode();
//        $this->verify_code = $verify_code = $this::createHexCode();
        $this->username = $values['username'];
        $this->email = $values['email'];
        $salt = $this::createSalt(32);
        $iterations = 10000;
        $hash = $this::hashPassword($values['password'], $salt, $iterations);

        $data = ['username' => $values['username'], 'email' => $values['email'], 'password' => $hash, 'salt' => $salt, 'iterations' => $iterations];

        //$dbusr = new Database\UserAccounts();
        Database\UserAccounts::insertIntoDatabase($data);
        //unset($dbusr);
    }

    public function sendVerificationEmail(){
        $verify_code = $this::createHexCode(32);
        $delete_code = $this::createHexCode(32);
        Database\UserAccounts::updateVerifyCode(['username' => $this->username, 'email' => $this->email, 'verify_code' => $verify_code, 'delete_code' => $delete_code]);
        
        //putting email into database
        $user_code='0';
        //send verification email
        $subject='verify your account email for ditup.org';
        $message_body='Hello ' . $this->username . ',<br /> thank you for joining. To verify your email address (' . $this->email . ') and to finish the process, follow the link <br /> http://ditup.org/signup/verify/' . $this->username . '/' . $verify_code . ' <br /> If your email address was misused by somebody else, follow the link:<br /> http://ditup.org/signup/delete/' . $this->username . '/' . $delete_code . '<br /> to reset the signup process. <br /><br />Both links will be valid for next 6 hours.';
        $headers='From: noreply@ditup.org';
        $mail_accepted = mail ( $this->email , $subject , $message_body , $headers );
        $mail_accepted ? print ('mail accepted') : print ('mail not accepted');
    }
    
    private static function createSalt($length = 32){
        $is_secure;
        $salt = openssl_random_pseudo_bytes($length, $is_secure);

        if($is_secure){
            return $salt;
        }
        else{
            throw new Exception('salt is not secure');
        }
    }

    private static function createHexCode($length = 32){
        $is_secure;
        $code = bin2hex(openssl_random_pseudo_bytes($length/2.0+1, $is_secure));
        return substr($code, 0, $length);
    }

    private static function hashPassword($password, $salt, $iterations){
        return hash_pbkdf2 ('sha256' , $password , $salt, $iterations);
    }

    private static function compareHashes($a, $b) { 
        if (!is_string($a) || !is_string($b)) { 
            return false; 
         } 

        $len = strlen($a); 
        if ($len !== strlen($b)) { 
            return false; 
        }

        $status = 0;
        for ($i = 0; $i < $len; $i++) { 
            $status |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $status === 0;
    }

    private static function validateEmail($email) {
        $email_match='/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/';
        return preg_match($email_match,$email)?true:false;
    }

    private static function validateUsername($username) {
        $username_match='/^([a-z0-9_\-\.]{2,16})$/';
        return preg_match($username_match, $username)?true:false;
    }

    private function usernameUnique($username) {
        return !$this->exists($username);
    }

    private function emailUnique($email) {
        $data = Database\UserAccounts::selectAccountByEmail($email);
        return sizeof($data) === 0 ? true : false;
    }

    private static function validatePassword($password) {
        /*** length of password is 8 to 128 characters to prevent DDoS attack by letting too long password process through secure hashing functions... ***/
        //$password_match='/(?=^.{8,}$)(?=.[a-zA-Z0-9])(?=.[^a-zA-Z0-9]).*$/';
        $password_match='/(?=^.{8,128}$)(?=.*[a-zA-Z0-9])(?=.*[^A-Za-z0-9]).*$/';
        return preg_match($password_match, $password)?true:false;
    }
    
    /**
     *$values={email,username,password,password2}
     *&$errors=array;
     */

    public function validate($values,&$errors){
        $ret=true;
        if(!$this::validateEmail($values['email'])){
            $ret = false;
            $errors['email'] = 'email address is empty or not valid';
        }
        if(!$this::validateUsername($values['username'])){
            $ret = false;
            $errors['username'] = 'username must be 2-16 characters long and contain only a-z, 0-9, _, -, .';
        }
        if(!$this->usernameUnique($values['username'])){
            $ret = false;
            $msg = 'username already exists';
            if(isset($errors['username'])){
                $errors['username'] .= '; ' . $msg;
            }
            else{
                $errors['username'] = $msg;
            }
        }
        if(!$this->emailUnique($values['email'])){
            $ret = false;
            $msg = 'account with this email already exists';
            if(isset($errors['email'])){
                $errors['email'] .= '; ' . $msg;
            }
            else{
                $errors['email'] = $msg;
            }
        }
        if(!$this::validatePassword($values['password'])){
            $ret = false;
            $errors['password'] = 'password must be 8-128 characters long and contain some of [a-zA-Z0-9] and some special characters';
        }
        if($values['password']!==$values['password2']){
            $ret = false;
            $errors['password2'] = 'passwords don\'t match';
        }

        return $ret;
    }

}
