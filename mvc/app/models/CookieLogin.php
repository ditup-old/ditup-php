<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/User.php';
require_once dirname(__FILE__).'/database/CookieLogin.php';

class CookieLogin extends User
{
    const COOKIE_PASS_LENGTH = 64;
    const COOKIE_CODE_LENGTH = 32;
    const COOKIE_NAME = 'cookie_login';
    const COOKIE_TIME = 604800;


    public static function createLoginCookie($values = []){
        //$values=[username]
        //generate cookie_code
        $cookie_code = parent::createHexCode(self::COOKIE_CODE_LENGTH);
        //generate password
        $pass_is_secure;
        $password = parent::createHexCode(self::COOKIE_PASS_LENGTH, $pass_is_secure);
        $salt = parent::createSalt(parent::PASSWORD_SALT_LENGTH);
        $iterations = parent::PASSWORD_HASH_ITERATIONS;
        $hash_password = parent::hashPassword($password, $salt, $iterations);

        //create new cookie
        if(Database\CookieLogin::insertCookie([
            'username' => $values['username'],
            'cookie_code' => $cookie_code,
            'hash_password' => $hash_password,
            'salt' => $salt,
            'iterations' => $iterations
        ])){
            setcookie(self::COOKIE_NAME, $values['username'].' '.$cookie_code.' '.$password, time()+self::COOKIE_TIME, '/');
        }
    }

    public static function cleanLoginCookies(){
        //delete old cookies from database;
        Database\CookieLogin::deleteOldCookies();
    }

    public static function destroyLoginCookie(){
        $data = self::getCookieData();
        if(!!$data){
            Database\CookieLogin::deleteCookie([
                'username' => $data['username'],
                'cookie_code' => $data['cookie_code']
            ]);
            self::deleteLoginCookie();
            //echo 'cookie destroyed';
        }
    }

    public static function destroyAllLoginCookies($values){
        //$values = [username]
        Database\CookieLogin::deleteAllCookies(['username' => $values['username']]);
        self::deleteLoginCookie();
    }

    private static function deleteLoginCookie(){
        if(isset($_COOKIE[self::COOKIE_NAME])){
            setcookie(self::COOKIE_NAME, '', 1, '/');
        }
    }

    private static function getCookieData(){
        if(isset($_COOKIE[self::COOKIE_NAME])){
            $login_data = explode(' ', $_COOKIE[self::COOKIE_NAME]);
            if(sizeof($login_data) === 3){
                $username = $login_data[0];
                $cookie_code = $login_data[1];
                $password = $login_data[2];
                return [
                    'username' => $username,
                    'cookie_code' => $cookie_code,
                    'password' => $password
                ];
            }
            else{
                setcookie(self::COOKIE_NAME, '', 1, '/');
                return false;
            }
        }
        else return false;
    }

    public static function authenticate(){
            $data = self::getCookieData();
//            print_r($data);
            if(!!$data){
                $username = $data['username'];
                $cookie_code = $data['cookie_code'];
                $password = $data['password'];
                $db_cookie = Database\CookieLogin::selectCookie(['username' => $username, 'cookie_code' => $cookie_code]);
//                echo '<br />'.print_r($db_cookie,true);
                if($db_cookie){
                    $hash_password = parent::hashPassword($password, $db_cookie['salt'], $db_cookie['iterations']);
//                    echo '<br />'.$hash_password.'<br />'.$password.'<br />'.$db_cookie['salt'].'<br />'. $db_cookie['iterations'];
//                    echo '<br />' .$hash_password.'<br />'.print_r($db_cookie, true);
                    if(parent::compareHashes($hash_password, $db_cookie['hash_password'])){
                        self::refreshLoginCookie([
                            'username' => $username,
                            'cookie_code' => $cookie_code
                        ]);
                        parent::updateLastLogin($username);
                        return $username;
                    }
                    else return false;
                }
                else return false;
            }
            else return false;
    }

    public static function refreshLoginCookie($values){
        /***
        this function should change cookie pass and update cookie 
        
        *$values = [username, cookie_code]****/
        

        ///create new pass and salt and hash of the pass
        $new_pass_is_secure;
        $new_pass = self::createHexCode(self::COOKIE_PASS_LENGTH, $new_pass_is_secure);
        $salt = self::createSalt(self::PASSWORD_SALT_LENGTH);
        $hashed_pass = self::hashPassword($new_pass, $salt, self::PASSWORD_HASH_ITERATIONS);
        
        if($new_pass_is_secure){
            //echo 'updating cookie';
            Database\CookieLogin::updateCookie([
                'username' => $values['username'],
                'cookie_code' => $values['cookie_code'],
                'hash_password' => $hashed_pass,
                'salt' => $salt,
                'iterations' => self::PASSWORD_HASH_ITERATIONS
            ]);
            setCookie(self::COOKIE_NAME, '', 1);
            setCookie(self::COOKIE_NAME, $values['username'].' '.$values['cookie_code'].' '.$new_pass, time()+self::COOKIE_TIME);
            ///enter the hash, salt and iterations to the database (cookie_login)
        }
        else throw new Exception('new cookie pass is not secure');
        ///return new pass (to be used for cookie creation)
    }
}
