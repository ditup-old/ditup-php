<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class Notifications{
    
    public static function insertNotification(){
        
    }

    public static function countNewNotifications($username){
        
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
