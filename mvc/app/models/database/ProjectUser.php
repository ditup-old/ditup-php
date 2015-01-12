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
}
