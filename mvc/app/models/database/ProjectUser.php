<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class ProjectUser
{
    public static function selectProjectsByUsername($username){
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
            $statement = $pdo->prepare('SELECT pr.projectname, pr.url, pr.visibility, pu.relationship, pu.joined FROM user_accounts AS ua INNER JOIN project_user AS pu ON ua.user_id=pu.user_id INNER JOIN projects AS pr ON pu.project_id=pr.project_id WHERE ua.username = :un');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            unset($statement);

            $pdo->commit();
            unset($pdo);
            return $data;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            throw new Exception('database problem: ' . $e);
          // Report errors
        }
        unset($pdo);
    }

    public static function selectDitRelationshipByUsername($username, $dit_url){
        $pdo = self::newPDO();

        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
        // 
        try
        {
            // Prepare the statements
            $statement = $pdo->prepare('SELECT relationship FROM project_user
                WHERE user_id IN (SELECT user_id FROM user_accounts WHERE username=:un)
                AND project_id IN (SELECT project_id FROM projects WHERE url=:url)');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->bindValue(':url',strval($dit_url), PDO::PARAM_STR);
            $statement->execute();
            
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            unset($statement);
            
            $rels = [];
            foreach($data as $rel){
                $rels[]=$rel['relationship'];
            }

            $pdo->commit();
            unset($pdo);
            return $rels;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            throw new Exception('database problem: ' . $e);
          // Report errors
        }
        unset($pdo);
    }

    public static function insertAwaitMember($username, $project_url, $join_message, &$error=''){
        /**
         * this function will insert row of awaiting member to project_user table
         * 
         *
         */
        $pdo = self::newPDO();
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
        // 
        try
        {
            // Prepare the statements
            $statement = $pdo->prepare('INSERT INTO project_user (user_id, project_id, relationship, join_message)
                SELECT ua.user_id, dit.project_id, \'await-member\', :jmsg
                    FROM user_accounts AS ua, projects AS dit
                        WHERE ua.username=:uname AND dit.url=:url');
            $statement->bindValue(':uname',strval($username), PDO::PARAM_STR);
            $statement->bindValue(':url',strval($project_url), PDO::PARAM_STR);
            $statement->bindValue(':jmsg',strval($join_message), PDO::PARAM_STR);
            $statement->execute();
            
            unset($statement);

            $pdo->commit();
            unset($pdo);
            return true;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            unset($pdo);
            $error=print_r($e, false);
            return false;
            //throw new Exception('database problem: ' . $e);
        }
        unset($pdo);
        return false;
    }

    private static function newPDO(){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
    //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        return $pdo;
    }
}
