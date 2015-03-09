<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class Search
{
    public static function selectUsersSearch($szuk){
        $pdo = self::newPDO();
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
        // 
        try
        {
            $statement=$pdo->prepare('SELECT username FROM user_accounts WHERE username COLLATE UTF8_GENERAL_CI LIKE :szuk');
            $statement->bindValue(':szuk',strval("%{$szuk}%"), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = $rows;

            $pdo->commit();

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

    public static function selectDitsSearch($szuk){
        $pdo = self::newPDO();
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
        // 
        try
        {
            $statement=$pdo->prepare('SELECT projectname ditname, url, type FROM projects
            WHERE projectname COLLATE UTF8_GENERAL_CI LIKE :szuk
            OR url LIKE :szuk2
            ');
            $statement->bindValue(':szuk',strval("%{$szuk}%"), PDO::PARAM_STR);
            $statement->bindValue(':szuk2',strval("%{$szuk}%"), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = $rows;

            $pdo->commit();

            unset ($pdo);
            //exit(print_r($data, true));
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

    private static function newPDO(){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
    //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        return $pdo;
    }
}
