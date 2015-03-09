<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class Notifications
{
    
    public static function insertNotification($type, $values){
        
    }

    public static function updateNotificationViewTime($id, $username){
        $pdo=self::newPDO();
        $pdo->beginTransaction();

        try
        {
            $statement=$pdo->prepare('UPDATE notifications SET view_time=UNIX_TIMESTAMP()
            WHERE view_time IS NULL
            AND notification_id=:id
            AND user_id IN
            (SELECT user_id FROM user_accounts WHERE username=:un)
            ');
            $statement->bindValue(':un', strval($username), PDO::PARAM_STR);
            $statement->bindValue(':id', strval($id), PDO::PARAM_STR);
            $statement->execute();

            $pdo->commit();

            unset($pdo);

            return true;
        }
        catch(PDOException $e){
            $pdo->rollBack();
            unset($pdo);
            exit(print_r($e, true));
        }
    }

    public static function selectNotification($id, $username){
        $pdo=self::newPDO();
        $pdo->beginTransaction();

        try
        {
            $statement=$pdo->prepare('SELECT nt.*, ua.username username, dits.url url, dits.projectname ditname, dits.type dittype
                FROM notifications nt
                LEFT JOIN user_accounts ua ON ua.user_id=nt.about_user_id
                LEFT JOIN projects dits ON dits.project_id=nt.about_dit_id
                WHERE nt.notification_id=:id
                AND nt.user_id IN
                (SELECT user_id FROM user_accounts WHERE username=:un)
            ');
            $statement->bindValue(':un', strval($username), PDO::PARAM_STR);
            $statement->bindValue(':id', strval($id), PDO::PARAM_STR);
            $statement->execute();

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(sizeof($rows)===1){
                $notif = $rows[0];
                $pdo->commit();
                unset($pdo);
                return $notif;
            }
            elseif(sizeof($rows)===0){
                $pdo->rollBack();
                unset($pdo);
                return false;
            }
            else{
                $pdo->rollBack();
                unset($pdo);
                return null;
            }
        }
        catch(PDOException $e){
            $pdo->rollBack();
            unset($pdo);
            exit(print_r($e, true));
        }
    
    }

    public static function countNewNotifications($username){
        $pdo=self::newPDO();
        $pdo->beginTransaction();
        try
        {
            // Prepare the statements
            $statement=$pdo->prepare('SELECT COUNT(nt.user_id) ct FROM notifications nt
            INNER JOIN user_accounts ua ON ua.user_id=nt.user_id
            WHERE ua.username=:un
            AND nt.view_time IS NULL
            AND nt.create_time>ua.visit_notifications');        
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $no=(int)$rows[0]['ct'];
            $pdo->commit();

            unset($pdo);
            return $no;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            // Report errors
            unset($pdo);
            exit(print_r($e, true));
        }

    }

    public static function selectNotifications($username){
        $pdo=self::newPDO();
        $pdo->beginTransaction();
        try
        {
            // Prepare the statements
            $statement=$pdo->prepare('SELECT nt.notification_id id, nt.view_time view_time, nt.type type, ua.username, dit.projectname ditname, dit.url, dit.type dittype FROM notifications nt
            LEFT JOIN user_accounts ua ON ua.user_id=nt.about_user_id
            LEFT JOIN projects dit ON dit.project_id=nt.about_dit_id
            WHERE nt.user_id IN
            (SELECT user_id FROM user_accounts WHERE username=:un)');        
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $notes=$rows;
            $pdo->commit();

            unset($pdo);
            return $notes;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            // Report errors
            unset($pdo);
            exit(print_r($e, true));
        }

    }

    public static function deleteNotification($id, $username){
        $pdo=self::newPDO();
        $pdo->beginTransaction();
        try
        {
            // Prepare the statements
            $statement=$pdo->prepare('DELETE FROM notifications
            WHERE notification_id=:id
            AND user_id IN
            (SELECT user_id FROM user_accounts WHERE username=:un)');        
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->bindValue(':id',strval($id), PDO::PARAM_STR);
            $statement->execute();
            
            $no = $statement->rowCount();
            if($no===1){
                $pdo->commit();
                unset($pdo);
                return true;
            }
            elseif($no===0){
                $pdo->rollBack();
                unset($pdo);
                return false;
            }
            else{
                $pdo->rollBack();
                unset($pdo);
                exit('error: multiple entry in database with similar id');
            }
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            // Report errors
            unset($pdo);
            exit(print_r($e, true));
        }

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
