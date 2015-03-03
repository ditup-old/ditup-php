<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;    //to be removed when DbAccess will be finished
use PDOException;   //to be removed when DbAccess will be finished
use Exception;

require_once dirname(__FILE__).'/db-login.php';
//require_once dirname(__FILE__).'/DbAccess.php';

class CookieLogin
{
//    selectCookie
//    insertCookie
//    deleteCookie
//    updateCookie
    
    public static function selectCookie(Array $values){
        if(isset($values, $values['username'], $values['cookie_code'])){
            $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
            //****************without these lines it will not catch error and not transaction well. not rollback.********
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
            $pdo->beginTransaction();
            // 
            try
            {
                // Prepare the statements
                $statement=$pdo->prepare('SELECT cl.hash_password, cl.iterations, cl.salt FROM cookie_login AS cl INNER JOIN user_accounts AS ua ON cl.user_id = ua.user_id WHERE ua.username=:un AND cl.cookie_code=:cc');
                $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
                $statement->bindValue(':cc',strval($values['cookie_code']), PDO::PARAM_STR);
                $statement->execute();
                
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                $data = $rows;
      //
                $pdo->commit();

                unset($pdo);
                return sizeof($data)>0 ? $data[0] : false;
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                unset($pdo);
                throw new Exception('database problem: ' . $e);
              // Report errors
            }
        }
        else{
        }
    }

    public static function deleteCookie(Array $values){
        if(isset($values, $values['username'], $values['cookie_code'])){
            $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
            //****************without these lines it will not catch error and not transaction well. not rollback.********
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
            $pdo->beginTransaction();
            // 
            try
            {
                // Prepare the statements
                
                $statement = $pdo->prepare('SELECT user_id FROM user_accounts WHERE username=:un');
                $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
                $statement->execute();
                
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                $user_id = $rows[0]['user_id'];

                $statement=$pdo->prepare('DELETE FROM cookie_login WHERE user_id=:uid AND cookie_code=:cc');
                $statement->bindValue(':uid', strval($user_id), PDO::PARAM_STR);
                $statement->bindValue(':cc', strval($values['cookie_code']), PDO::PARAM_STR);
                $statement->execute();
                
                $pdo->commit();
                unset($pdo);
                return true;
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                unset($pdo);
                throw new Exception('database problem: ' . $e);
              // Report errors
            }
            unset($pdo);
            // echo $data;
            return true;
        }
        else{
        }
    }

    public static function insertCookie(Array $values){
        if(isset($values, $values['username'], $values['cookie_code'], $values['hash_password'], $values['salt'], $values['iterations'])){
            $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
            //****************without these lines it will not catch error and not transaction well. not rollback.********
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
            $pdo->beginTransaction();
            // 
            try
            {
                // Prepare the statements
                $statement = $pdo->prepare('SELECT user_id FROM user_accounts WHERE username=:un');
                $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
                $statement->execute();
                
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                $user_id = $rows[0]['user_id'];

                $statement = $pdo->prepare('INSERT INTO cookie_login (user_id, cookie_code, hash_password, iterations, salt, login_time, refresh_time) VALUES (:uid, :cc, :pwd, :ite, :salt, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())');
                $statement->bindValue(':uid',strval($user_id), PDO::PARAM_STR);
                $statement->bindValue(':cc',strval($values['cookie_code']), PDO::PARAM_STR);
                $statement->bindValue(':pwd',strval($values['hash_password']), PDO::PARAM_STR);
                $statement->bindValue(':ite',strval($values['iterations']), PDO::PARAM_STR);
                $statement->bindValue(':salt',strval($values['salt']), PDO::PARAM_STR);
                $statement->execute();
      //
                $pdo->commit();
                unset($pdo);
                return true;
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                unset($pdo);
                throw new Exception('database problem: ' . $e);
              // Report errors
            }
        }
        else{
            //throw some exceptions
        }
    }

    public static function updateCookie(Array $values){
        if(isset($values, $values['username'], $values['cookie_code'], $values['hash_password'], $values['salt'], $values['iterations'])){
            $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
            //****************without these lines it will not catch error and not transaction well. not rollback.********
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
            $pdo->beginTransaction();
            // 
            try
            {
                // Prepare the statements
                $statement = $pdo->prepare('SELECT user_id FROM user_accounts WHERE username=:un');
                $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
                $statement->execute();
                
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                $user_id = $rows[0]['user_id'];

                $statement = $pdo->prepare('UPDATE cookie_login SET hash_password=:pwd, iterations=:ite, salt=:salt, refresh_time=UNIX_TIMESTAMP() WHERE user_id=:uid AND cookie_code=:cc');
                $statement->bindValue(':uid',strval($user_id), PDO::PARAM_STR);
                $statement->bindValue(':cc',strval($values['cookie_code']), PDO::PARAM_STR);
                $statement->bindValue(':pwd',strval($values['hash_password']), PDO::PARAM_STR);
                $statement->bindValue(':ite',strval($values['iterations']), PDO::PARAM_STR);
                $statement->bindValue(':salt',strval($values['salt']), PDO::PARAM_STR);
                $statement->execute();
      //
                $pdo->commit();
                //echo 'in update in database';
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                throw new Exception('database problem: ' . $e);
              // Report errors
            }
            unset($pdo);
            // echo $data;
            //return $data;
        }
        else{
            //throw some exceptions
            throw new Exception('not provided correct data');
        }
    }
    
    public static function deleteOldCookies(){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
        //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
        // 
        try
        {
            // Prepare the statements

            $statement=$pdo->prepare('DELETE FROM cookie_login WHERE refresh_time<=(UNIX_TIMESTAMP()-3600*24*10)');
            //$statement->bindValue(':uid', strval($user_id), PDO::PARAM_STR);
            $statement->execute();

            $pdo->commit();
            unset($pdo);
            return true;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            unset($pdo);
            throw new Exception('database problem: ' . $e);
          // Report errors
        }
        unset($pdo);
        // echo $data;
        return true;
    }

    public static function deleteAllCookies(Array $values){
        if(isset($values, $values['username'])){
            $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
            //****************without these lines it will not catch error and not transaction well. not rollback.********
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
            $pdo->beginTransaction();
            // 
            try
            {
                // Prepare the statements
                
                $statement = $pdo->prepare('SELECT user_id FROM user_accounts WHERE username=:un');
                $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
                $statement->execute();
                
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                $user_id = $rows[0]['user_id'];

                $statement=$pdo->prepare('DELETE FROM cookie_login WHERE user_id=:uid');
                $statement->bindValue(':uid', strval($user_id), PDO::PARAM_STR);
                $statement->execute();
                
                $pdo->commit();
                unset($pdo);
                return true;
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                unset($pdo);
                throw new Exception('database problem: ' . $e);
              // Report errors
            }
            unset($pdo);
            // echo $data;
            return true;
        }
        else{
        }
    }
}
