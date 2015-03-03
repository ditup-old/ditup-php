<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;    //to be removed when DbAccess will be finished
use PDOException;   //to be removed when DbAccess will be finished
use Exception;

require_once dirname(__FILE__).'/db-login.php';
require_once dirname(__FILE__).'/DbAccess.php';

class UserAccounts extends DbAccess
{

    public static function insertIntoDatabase(Array $values){
        if(isset($values, $values['username'], $values['email'], $values['password'], $values['salt'], $values['iterations'])){
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
                $statement = $pdo->prepare('INSERT INTO user_accounts (username, email, password, salt, iterations, account_created) VALUES (:un, :em, :pwd, :salt, :it, UNIX_TIMESTAMP())');
                $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
                $statement->bindValue(':em',strval($values['email']), PDO::PARAM_STR);
                $statement->bindValue(':pwd',strval($values['password']), PDO::PARAM_STR);
                $statement->bindValue(':salt',strval($values['salt']), PDO::PARAM_STR);
                $statement->bindValue(':it',strval($values['iterations']), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);
                
                $new_user_id = $pdo->lastInsertId();

                $statement = $pdo->prepare('INSERT INTO user_info (user_id) VALUES (:uid)');
                $statement->bindValue(':uid',strval($new_user_id), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);

                //$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                //$data = $rows;
      //
                $pdo->commit();
                echo 'entered data to database';
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
            if(!isset($values)) throw new Exception ('Users::insertIntoDatabase Error: array of values must be provided!');
            elseif(!isset($values['username'])) throw new Exception ('Users::insertIntoDatabase Error: username must be provided!');
            elseif(!isset($values['email'])) throw new Exception ('Users::insertIntoDatabase Error: email must be provided!');
            elseif(!isset($values['password'])) throw new Exception ('Users::insertIntoDatabase Error: hashed password must be provided!');
            elseif(!isset($values['salt'])) throw new Exception ('Users::insertIntoDatabase Error: salt must be provided!');
            elseif(!isset($values['iterations'])) throw new Exception ('Users::insertIntoDatabase Error: number of iterations must be provided!');
            else throw new Exception ('general exception');
        }
    }
    
    public static function updateLastLogin($username){
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
            $statement = $pdo->prepare('UPDATE user_accounts SET last_login=UNIX_TIMESTAMP() WHERE username=:un');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            unset($statement);
            
            //$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            //$data = $rows;
  //
            $pdo->commit();
            //echo 'entered data to database';
            unset($pdo);
            return true;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            unset($pdo);
            print_r($e);
            return false;
            //throw new Exception('database problem: ' . $e);
            // Report errors
        }
        unset($pdo);
        // echo $data;
        //return $data;
    }

    public function updateVerifyCode($values){
        if(isset($values, $values['username'], $values['email'], $values['verify_code'], $values['delete_code'])){
            
            $this->dbConnect();
            $this->dbExecute('UPDATE user_accounts SET verify_code=:vc, delete_code=:dc, code_created=UNIX_TIMESTAMP() WHERE username=:un AND email=:em', [':un' => $values['username'], ':em' => $values['email'], ':vc' => $values['verify_code'], ':dc' => $values['delete_code']]);
            $this->dbDisconnect();
        }
        else{
            if(!isset($values)) throw new Exception ('Users::updateVerifyCode Error: array of values must be provided!');
            elseif(!isset($values['username'])) throw new Exception ('Users::updateVerifyCode Error: username must be provided!');
            elseif(!isset($values['email'])) throw new Exception ('Users::updateVerifyCode Error: email must be provided!');
            elseif(!isset($values['verify_code'])) throw new Exception ('Users::updateVerifyCode Error: verification code must be provided!');
            elseif(!isset($values['delete_code'])) throw new Exception ('Users::updateVerifyCode Error: deletion code must be provided!');
            else throw new Exception ('general exception, debugging will be needed');
        }
    }

    public static function selectAccountByUsername($username){        
        require_once dirname(__FILE__).'/db-login.php';
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
            $statement=$pdo->prepare('SELECT * FROM user_accounts WHERE username=:un');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data=$rows;
  // 
            $pdo->commit();
        }
        catch(PDOException $e)
        {
          $pdo->rollBack();
      
          //$outcome=htmlentities(print_r($e,true));
          // Report errors
        }
        unset($pdo);

        return $data;
    }    
    
    public function updateVerified($values){
        if(isset($values, $values['username'], $values['verify_code'])){
            require_once dirname(__FILE__).'/db-login.php';
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
                $statement=$pdo->prepare('UPDATE user_accounts SET verified=TRUE WHERE username=:un AND verify_code=:vc AND (UNIX_TIMESTAMP()-code_created)<21600');
                $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
                $statement->bindValue(':vc',strval($values['verify_code']), PDO::PARAM_STR);
                $statement->execute();
                $affected_rows = $statement->rowCount();
            
                // 
                $pdo->commit();
                unset($pdo);
                return $affected_rows>=1 ? true : false;
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                unset($pdo);
          
                $outcome=htmlentities(print_r($e,true));
                echo $outcome;
            }
        }
        else{
            if(!isset($values)) throw new Exception ('Users::updateVerifyCode Error: array of values must be provided!');
            elseif(!isset($values['username'])) throw new Exception ('Users::updateVerifyCode Error: username must be provided!');
            elseif(!isset($values['verify_code'])) throw new Exception ('Users::updateVerifyCode Error: verification code must be provided!');
            else throw new Exception ('general exception, debugging will be needed');
        }
    }
    
    public static function selectAccountByEmail($email){        
        require_once dirname(__FILE__).'/db-login.php';
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
            $statement=$pdo->prepare('SELECT * FROM user_accounts WHERE email=:email');
            $statement->bindValue(':email',strval($email), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data=$rows;
  // 
            $pdo->commit();
        }
        catch(PDOException $e)
        {
          $pdo->rollBack();
      
          //$outcome=htmlentities(print_r($e,true));
          // Report errors
        }
        unset($pdo);

        return $data;
    }

    public static function updatePassword(Array $values){
    /**
     * $values = Array(old-password, new-password, salt, iterations);
    ****/
        require_once dirname(__FILE__).'/db-login.php';
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
            $statement=$pdo->prepare('UPDATE user_accounts SET password=:pwd, salt=:sa, iterations=:it WHERE username=:un');
            $statement->bindValue(':pwd',strval($values['password']), PDO::PARAM_STR);
            $statement->bindValue(':sa',strval($values['salt']), PDO::PARAM_STR);
            $statement->bindValue(':it',strval($values['iterations']), PDO::PARAM_STR);
            $statement->bindValue(':un',strval($values['username']), PDO::PARAM_STR);
            $statement->execute();
            
            $pdo->commit();
            
            unset($pdo);
            return true;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            // Report errors
            print_r($e);
            unset($pdo);
            exit();
        }
    }

    public static function updateVisitReceivedMessages($username){
    /**
     * this function will update time of last visit of received messages to now.
     * used to show how many unread, not seen messages user has since last visit of received messages
     */
        require_once dirname(__FILE__).'/db-login.php';
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
            $statement=$pdo->prepare('UPDATE user_accounts SET visit_received_messages=UNIX_TIMESTAMP() WHERE username=:un');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->rowCount();
            unset($statement);
            if($rows===1){
                $pdo->commit();
                unset($pdo);
                return true;
            }
            else{
                $pdo->rollBack();
                unset($pdo);
                return false;
            }


        }
        catch(PDOException $e)
        {
          $pdo->rollBack();
      
          echo print_r($e,true);
          return false;

          // Report errors
        }
        unset($pdo);

    }

    public static function countUsers($username, $time=null){
    /**
     * return number of users who were active in last $time seconds.
     * if $time===null, return number of all users.
     * else return false
     */
        require_once dirname(__FILE__).'/db-login.php';
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
            if($time===null){
                $statement=$pdo->prepare('SELECT COUNT(ua.user_id) no FROM user_accounts ua
                WHERE ua.verified
                ');
            }
            else{
                $statement=$pdo->prepare('SELECT COUNT(ua.user_id) no FROM user_accounts ua
                WHERE (UNIX_TIMESTAMP()-ua.last_login)<:time
                AND ua.last_login IS NOT NULL
                AND ua.verified
                ');
                $statement->bindValue(':time',strval($time), PDO::PARAM_STR);
            }
            $statement->execute();

            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            $no=$data['0']['no']*1;
            unset($pdo);
            return $no;
        }
        catch(PDOException $e)
        {
          $pdo->rollBack();
      
          echo print_r($e,true);
          return false;

          // Report errors
        }
        unset($pdo);

    }
}
