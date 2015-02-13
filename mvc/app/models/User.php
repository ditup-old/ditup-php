<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/UserAccounts.php';

class User
{
    /***for function changePassword***/
    const ERROR_USERNAME_NOT_EXIST = 'username doesn\'t exist';
    const ERROR_PASSWORD_INCORRECT = 'password is incorrect';
    const ERROR_PASSWORDS_DIFFERENT = 'passwords don\'t match';
    const PASSWORD_REGEX = '/(?=^.{8,128}$)(?=.*[a-zA-Z0-9])(?=.*[^A-Za-z0-9]).*$/';
    const ERROR_PASSWORD_INVALID = 'password must be 8-128 characters long and contain some of [a-zA-Z0-9] and some special characters';

    const PASSWORD_HASH_ITERATIONS = 10000;
    const PASSWORD_SALT_LENGTH = 64;
    
    protected $username;
    private $verify_code;
    private $delete_code;
    private $email;
    
    public function setUsername($username){
        $this->username=$username;
    }

    public function saveSettings($username, $settings, &$errors = []){
        $errors=[
            'save' => 'saving to database is not implemented'
        ];
        return false;
    }

    public function readSettings($username){
        return [
            'visibility' => 'loggedin',
            'searchability' => 'nobody',
        ];
    }

    public function login($values, &$errors = null){
        if(isset($values, $values['username'], $values['password'])){
            
            $loggedin = false;
            $errors = [];

            $dbusr = new Database\UserAccounts();
            $data = Database\UserAccounts::selectAccountByUsername($values['username']);
            unset($dbusr);

            //print_r($data);
            if(sizeof($data) === 1 && isset($data[0]['salt'], $data[0]['password'], $data[0]['verified'], $data[0]['iterations'])){
                if($this::compareHashes($data[0]['password'], $this::hashPassword($values['password'], $data[0]['salt'], $data[0]['iterations']))){
                    if($data[0]['verified']){
                        $loggedin = true;
                    }
                    else{
                        $errors ['verified']='logged in but not verified';
                    }
                }
                else{
                    $errors['login']='passwords not match';
                }
                //compare hashes>check if verified>logged in
            }
            elseif(sizeof($data)===0){
                $errors['login']= 'user not exist';
            }
            else{
                throw new Exception('database error: wrong amount of users or data corruption');
            }
            return $loggedin;
        }
        else throw new Exception('username and password must be provided');
    }
    
    public static function exists($username){
//        echo dirname(__FILE__);
//        require_once dirname(__FILE__).'/database/UserAccounts.php';
        //$dbusr = new Database\UserAccounts();
        $data = Database\UserAccounts::selectAccountByUsername($username);
        //unset($dbusr);
        return sizeof($data) > 0 ? true : false;
    }

/** this function creates new user in database and sets*********/

    public function create(Array $values){
        $this->username = $values['username'];
        $this->email = $values['email'];
        $salt = self::createSalt(self::PASSWORD_SALT_LENGTH);
        $iterations = self::PASSWORD_HASH_ITERATIONS;
        $hash = self::hashPassword($values['password'], $salt, $iterations);

        $data = ['username' => $values['username'], 'email' => $values['email'], 'password' => $hash, 'salt' => $salt, 'iterations' => $iterations];

        $dbua = new Database\UserAccounts();
        $dbua->insertIntoDatabase($data);
        unset($dbua);
        return true;
    }

    public function sendVerificationEmail(){
        $verify_code = $this::createHexCode(32);
        $delete_code = $this::createHexCode(32);
        $dbua=new Database\UserAccounts;
        $dbua->updateVerifyCode(['username' => $this->username, 'email' => $this->email, 'verify_code' => $verify_code, 'delete_code' => $delete_code]);
        unset($dbua);

        //putting email into database
        $user_code='0';
        //send verification email
        $subject='verify your account email for ditup.org';
        $message_body='Hello ' . $this->username . ",\n\nthank you for joining. To verify your email address (" . $this->email . ") and to finish the process, follow the link\nhttp://ditup.org/signup/verify/" . $this->username . '/' . $verify_code . "\n\nIf your email address was misused by somebody else, follow the link\nhttp://ditup.org/signup/delete/" . $this->username . '/' . $delete_code . "\nto reset the signup process.\n\nBoth links will be valid for next 6 hours.";
        $headers='From: noreply@ditup.org';
        $mail_accepted = mail ( $this->email , $subject , $message_body , $headers );
        $mail_accepted ? print ('mail accepted') : print ('mail not accepted');
    }
    
    protected static function createSalt($length = 64){
        $is_secure;
        //$salt = openssl_random_pseudo_bytes($length, $is_secure);
        
        $salt = substr(bin2hex(openssl_random_pseudo_bytes($length/2.0+1, $is_secure)), 0, $length);
        if($is_secure){
            return $salt;
        }
        else{
            throw new Exception('salt is not secure');
        }
    }

    protected static function createHexCode($length = 32, &$is_secure=null){
        $is_secure;
        $code = bin2hex(openssl_random_pseudo_bytes($length/2.0+1, $is_secure));
        return substr($code, 0, $length);
    }

    public static function hashPassword($password, $salt, $iterations){
        return hash_pbkdf2 ('sha256' , $password , $salt, $iterations);
    }

    public static function compareHashes($a, $b) { 
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
        return preg_match(self::PASSWORD_REGEX, $password)?true:false;
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
            $errors['password'] = self::ERROR_PASSWORD_INVALID;'password must be 8-128 characters long and contain some of [a-zA-Z0-9] and some special characters';
        }
        if($values['password']!==$values['password2']){
            $ret = false;
            $errors['password2'] = 'passwords don\'t match';
        }

        return $ret;
    }

    public function verify($values){
        $ua = new Database\UserAccounts;
        $ret=$ua->updateVerified($values);
        unset($ua);
        return $ret;
    }

    public function changePassword($values, &$errors){
        /****this function should cover all the process of changing password in database (validation and entering to database)**/
        /***$values: array (username, old-password, new-password, new-password2)***/
        if(isset($values, $values['username'], $values['old-password'], $values['new-password'], $values['new-password2'])){
            $username = $values['username'];
            $old_password = $values['old-password'];
            $new_password = $values['new-password'];
            $new_password2 = $values['new-password2'];
            $errors = [
                'old-password'=>[],
                'new-password'=>[],
                'new-password2'=>[]
            ];
            ///0. validate
            $valid = true;
            /////0.0 old pass matches;
            if(!$this->login(['username' => $username, 'password' => $old_password, []])){
                $valid=false;
                $errors['old-password'][]=self::ERROR_PASSWORD_INCORRECT;
            }
            /////0.1 new passes are equal;
            if($new_password!==$new_password2){
                $valid=false;
                $errors['new-password2'][] = self::ERROR_PASSWORDS_DIFFERENT;
            }
            /////0.2 new password is valid (meets password requirements);
            if(!self::validatePassword($new_password)){
                $valid=false;
                $errors['new-password'][] = self::ERROR_PASSWORD_INVALID;
            }
            ///1. enter to database
            if($valid){
                $salt = self::createSalt(self::PASSWORD_SALT_LENGTH);
                $hash = self::hashPassword($values['new-password'], $salt, self::PASSWORD_HASH_ITERATIONS);
                Database\UserAccounts::updatePassword([
                    'username' => $username,
                    'password' => $hash,
                    'salt' => $salt,
                    'iterations' => self::PASSWORD_HASH_ITERATIONS
                ]);
                return true;
            }
            else{
                return false;
            }
        }
        else throw new Exception('first parameter should be array(username, old-password, new-password, new-password2)');
    }

}
