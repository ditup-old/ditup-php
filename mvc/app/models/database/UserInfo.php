<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class UserInfo
{
    public static function selectProfile($username)
    {
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
            //print_r($username);
            $statement=$pdo->prepare('SELECT ua.username, ui.* FROM user_accounts AS ua INNER JOIN user_info AS ui ON ua.user_id = ui.user_id WHERE ua.username=:un');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
  // 
            $pdo->commit();

            if(sizeof($rows)>1) throw Exception ('database error: more than one row of user info for user '.$username);
            elseif(sizeof($rows)==1) $data=$rows[0];
            else return false;
            unset ($pdo);
            return $data;

        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
        }
        unset($pdo);
    }

    public static function updateProfile($parameters){
        /***this function will enter profile data to database
        parameters: all the necessary data for profile update
        **/
        
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
            $statement=$pdo->prepare('SELECT user_id FROM user_accounts WHERE username = :un');
            $statement->bindValue(':un',strval($parameters['username']), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            unset($statement);
            if(sizeof($rows)==1) $user_id=$rows[0]['user_id'];
            else throw Exception('database error: either none or more usernames exist in database');

            $statement=$pdo->prepare('UPDATE user_info SET v_about=:vabout, about=:about, v_age=:vage, birthday=:bd, v_website=:vweb, website=:web, v_bewelcome=:vbw, bewelcome=:bw, v_gender=:vgen, gender=:gen WHERE user_id=:uid');
            $statement->bindValue(':uid',    strval($user_id                  ), PDO::PARAM_STR);
            $statement->bindValue(':vabout', strval($parameters['v_about']    ), PDO::PARAM_STR);
            $statement->bindValue(':about',  strval($parameters['about']      ), PDO::PARAM_STR);
            $statement->bindValue(':vage',   strval($parameters['v_age']      ), PDO::PARAM_STR);
            $statement->bindValue(':bd',     strval($parameters['birthday']   ), PDO::PARAM_STR);
            $statement->bindValue(':vweb',   strval($parameters['v_website']  ), PDO::PARAM_STR);
            $statement->bindValue(':web',    strval($parameters['website']    ), PDO::PARAM_STR);
            $statement->bindValue(':vbw',    strval($parameters['v_bewelcome']), PDO::PARAM_STR);
            $statement->bindValue(':bw',     strval($parameters['bewelcome']  ), PDO::PARAM_STR);
            $statement->bindValue(':vgen',   strval($parameters['v_gender']   ), PDO::PARAM_STR);
            $statement->bindValue(':gen',    strval($parameters['gender']     ), PDO::PARAM_STR);
            $statement->execute();

            $pdo->commit();

            unset ($pdo, $statement);
            return true;

        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
        }
        unset($pdo);

        return false;
    }
}
