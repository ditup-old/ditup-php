<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class Messages{
    
    /*****
    needs:
    create new message, either send it or write draft.

    insertMessage($values)
        $values = [from_project, from_user, to_users=[username, username], to_projects=[url, url], subject, message, sent=true/false, ]
    updateMessage($)
    selectMessagesOfProject($projectname, $incoming_or_outgoing);
    selectMessagesOfUser($username, $incoming_or_outgoing);
    rightToRead($username, $create_time, $request_username);
    selectMessage($username, $create_time)
    deleteMessage($username, $create_time);
    ******/

    public static function selectMessagesOfProject($url, $flag){
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
            if($flag==='SENT'){
                $query='SELECT pr.projectname, pr.url FROM projects AS pr
                    INNER JOIN ';
            }
            elseif($flag==='RECEIVED'){
                $query='';
            }
            $statement=$pdo->prepare($query);
            $statement->bindValue(':url',strval($url), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
  // 
            $pdo->commit();

            $data = $rows;
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

    public static function selectMessageIdsOfUser($username, $flag='RECEIVED'){
        /**
         * return array of message_id(s) of user with $username
         flags: SENT
                RECEIVED 
                //ALL 
                DRAFTS
         */
        $pdo = self::newPDO();
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
        // 
        try
        {
            $statement=$pdo->prepare('SELECT user_id from user_accounts WHERE username=:un');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            //print_r($rows);
            if(isset($rows[0], $rows[0]['user_id'])){
                $user_id=$rows[0]['user_id'];
            }
            else return false;

            //echo "\n".$user_id."\n";

            //SENT
            if($flag=='SENT'){
                $query='SELECT DISTINCT message_id FROM messages
                WHERE send_time IS NOT NULL
                AND from_user_id = :uid';
            }

            //DRAFTS
            elseif($flag=='DRAFTS'){
                $query='SELECT DISTINCT message_id FROM messages
                WHERE send_time IS NULL
                AND from_user_id = :uid';
            }
            //RECEIVED
            elseif($flag=='RECEIVED'){
                $query='
                SELECT DISTINCT msg.message_id FROM messages AS msg
                JOIN message_to_user AS mtu ON msg.message_id=mtu.message_id
                JOIN message_to_project AS mtp ON msg.message_id=mtp.message_id
                WHERE mtu.user_id=@uid
                OR mtp.message_id IN
                    (SELECT pu.project_id FROM project_user AS pu, (SELECT @uid:=:uid) AS v WHERE user_id = @uid)';
            }
            /*
            $query1='SELECT DISTINCT message_id FROM message_to_user, message_to_project WHERE
            send_time IS NOT NULL
            AND user_id=:uid';


            $query2 = 'SELECT DISTINCT message_id FROM message_to_project WHERE
            send_time IS NOT NULL
            AND project_id IN (
                SELECT project_id FROM project_user WHERE user_id=:uid
            )';
            */

            // Prepare the statements
            $statement = $pdo->prepare($query);
            $statement->bindValue(':uid',strval($user_id), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
  // 
            $pdo->commit();

            $data=[];
            for($i=0, $len=sizeof($rows); $i<$len; $i++) $data[]=$rows[$i]['message_id'];
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

    public static function rightToRead($username, $create_time, $request_username){
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
            
            //get user_id of request_user ($request_user_id)
            $statement=$pdo->prepare('SELECT user_id from user_accounts WHERE username=:run');
            $statement->bindValue(':run',strval($request_username), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(isset($rows[0], $rows[0]['user_id'])){
                $request_user_id=$rows[0]['user_id'];
            }
            else return false;

            //get message_id of message
            $statement=$pdo->prepare('SELECT msg.message_id AS mid FROM messages AS msg WHERE
                msg.create_time=:ct AND msg.from_user_id IN (SELECT user_id from user_accounts WHERE username=:un)
            ');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->bindValue(':ct',strval($create_time), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(isset($rows[0], $rows[0]['mid'])){
                $message_id=$rows[0]['mid'];
            }
            else return false;

            //if the message is written by request_user, she can read it.
            if($username=$request_username) return true;
            
            //if the message is addressed to the request_user, she can read it.
            $statement=$pdo->prepare('SELECT COUNT(message_id) AS cmi FROM message_to_user WHERE message_id=:msg_id AND user_id=:ruid)');
            $statement->bindValue(':msg_id',strval($message_id), PDO::PARAM_STR);
            $statement->bindValue(':ruid',strval($request_user_id), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if($rows[0]['cmi']>0) return true;

            //if the request_user is member of group which sent the message, she can read it.

            $statement=$pdo->prepare('SELECT COUNT(msg.message_id) AS cmi FROM messages AS msg
                INNER JOIN project_user AS prus ON prus.project_id=msg.from_project_id WHERE
                prus.user_id=:ruid AND mtp.message_id=:msg_id
                AND prus.relationship IN (\'admin\',\'member\')
                ');
            $statement->bindValue(':msg_id',strval($message_id), PDO::PARAM_STR);
            $statement->bindValue(':ruid',strval($request_user_id), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if($rows[0]['cmi']>0) return true;

            //if the request_user is member of group which received the message, she can read it.
            $statement=$pdo->prepare('SELECT COUNT(mtp.message_id) AS cmi FROM message_to_project AS mtp
                INNER JOIN project_user AS prus ON prus.project_id=mtp.project_id WHERE
                prus.user_id=:ruid AND mtp.message_id=:msg_id
                AND prus.relationship IN (\'admin\',\'member\')
                ');
            $statement->bindValue(':msg_id',strval($message_id), PDO::PARAM_STR);
            $statement->bindValue(':ruid',strval($request_user_id), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if($rows[0]['cmi']>0) return true;

  // 
            $pdo->commit();

            $data = $rows;
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

    public static function selectMessageId($from_username, $create_time){
        $pdo=self::newPDO();
        $pdo->beginTransaction();

        try{
            $statement=$pdo->prepare('SELECT message_id FROM messages
                WHERE create_time=:ct
                AND from_user_id IN (SELECT user_id FROM user_accounts WHERE username=:fun)');
            $statement->bindValue(':fun',strval($from_username), PDO::PARAM_STR);
            $statement->bindValue(':ct',strval($create_time), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $id = isset($rows[0], $rows[0]['message_id']) ? $rows[0]['message_id'] : null;
            $pdo->commit();
            unset($pdo);
            return $id;
        }
        catch(PDOException $e){
            $pdo->rollBack();
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
            unset($pdo);
            return false;
        }
    }
    
    public static function updateMessageById($message_id, $values, $flag='DRAFT'){
        //flags: SEND: send message
        //       DRAFT: update and save draft
        $pdo=self::newPDO();
        $pdo->beginTransaction();
        // 
        try
        {
            // Prepare the statements

            //1. update the message itself
            //2. delete all old to_project, to_user
            //3. insert new to_project, to_user
            $flag = $values['sent'] == true? 'SEND' : 'DRAFT';

            $statement=$pdo->prepare('UPDATE messages msg SET
                msg.from_user_id=(SELECT ua.user_id FROM user_accounts ua WHERE username=:un),
                msg.from_project_id=(SELECT pr.project_id FROM projects pr WHERE url=:url),
                msg.subject=:sub,
                msg.message=:msg,
                msg.send_time='.($flag=='SEND'?'UNIX_TIMESTAMP()':'NULL').'
                WHERE msg.message_id=:mid AND msg.send_time IS NULL
            ');
            $statement->bindValue(':un',strval($values['from_user']), PDO::PARAM_STR);
            $statement->bindValue(':url',strval($values['from_project']), PDO::PARAM_STR);
            $statement->bindValue(':sub',strval($values['subject']), PDO::PARAM_STR);
            $statement->bindValue(':msg',strval($values['message']), PDO::PARAM_STR);
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            $statement=$pdo->prepare('DELETE FROM message_to_user WHERE message_id=:mid');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();

            $statement=$pdo->prepare('DELETE FROM message_to_project WHERE message_id=:mid');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();

            //insert receivers - users to table 'message_to_user'
            $to_users=$values['to_users'];
            for($i=0, $len=sizeof($to_users); $i<$len; $i++){
                //insert into message_to_user
                $statement=$pdo->prepare('INSERT INTO message_to_user (message_id, user_id)
                    SELECT :msg_id, ua.user_id FROM user_accounts AS ua WHERE ua.username=:un');
                $statement->bindValue(':msg_id',strval($message_id), PDO::PARAM_STR);
                $statement->bindValue(':un',strval($to_users[$i]), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);
            }

            //insert receivers: projects to table 'message_to_project'
            $to_projects=$values['to_projects'];
            for($i=0, $len=sizeof($to_projects); $i<$len; $i++){
                //insert into message_to_project
                $statement=$pdo->prepare('INSERT INTO message_to_project (message_id, project_id)
                    SELECT :msg_id, pr.project_id FROM projects AS pr WHERE pr.url=:url');
                $statement->bindValue(':msg_id',strval($message_id), PDO::PARAM_STR);
                $statement->bindValue(':url',strval($to_projects[$i]), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);
            }
            
            $statement=$pdo->prepare('SELECT msg.create_time AS ct, ua.username AS un FROM messages AS msg INNER JOIN user_accounts AS ua ON msg.from_user_id=ua.user_id WHERE msg.message_id=:msg_id ');
            $statement->bindValue(':msg_id', strval($message_id), PDO::PARAM_STR);
            $statement->execute();

            $ids = $statement->fetchAll(PDO::FETCH_ASSOC);

            $identification = isset($ids[0]) ? ['username'=>$ids[0]['un'], 'timestamp' => $ids[0]['ct']] : false;
            unset($statement);
  // 
            $pdo->commit();

            unset ($pdo);
            return $identification;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
            unset($pdo);
            return false;
        }
    }

    public static function selectMessageById($message_id){
        $pdo=self::newPDO();
        $pdo->beginTransaction();
        // 
        try
        {
            // Prepare the statements
            $statement=$pdo->prepare('SELECT ua.username AS from_username, pr.projectname AS from_projectname, pr.url AS from_url, msg.message_id, msg.subject, msg.message, msg.send_time, msg.read_time, msg.create_time FROM messages AS msg
                LEFT JOIN user_accounts AS ua ON msg.from_user_id=ua.user_id
                LEFT JOIN projects AS pr ON pr.project_id=msg.from_project_id
                WHERE msg.message_id=:mid
            ');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $message = isset($rows[0]) ?
                [
                    'from-user'=>[
                        'username'=>$rows[0]['from_username']
                    ],
                    'from-project'=>(
                    (isset($rows[0]['from_projectname'], $rows[0]['from_url']) && $rows[0]['from_projectname'] && $rows[0]['from_url'])
                    ?[
                        'projectname'=>$rows[0]['from_projectname'],
                        'url'=>$rows[0]['from_url']
                    ]
                    :null
                    ),
                    'subject'=>$rows[0]['subject'],
                    'message'=>$rows[0]['message'],
                    'send-time'=>$rows[0]['send_time'],
                    'read-time'=>$rows[0]['read_time'],
                    'create-time'=>$rows[0]['create_time'],
                    'to-users'=>[],
                    'to-projects'=>[]
                ]
            : false;

            if($message==false){
                $pdo->commit();
                unset($pdo);
                return false;
            }

            $message_id=$rows[0]['message_id'];
                /* 
                LEFT JOIN message_to_user AS mtu ON mtu.message_id=msg.message_id
                LEFT JOIN user_accounts AS uato ON uato.user_id=mtu.user_id
                LEFT JOIN message_to_project AS mtp ON mtp.message_id=msg.message_id
                LEFT JOIN projects AS prto ON prto.project_id=mtp.project_id
                */
            $statement=$pdo->prepare('SELECT username FROM user_accounts WHERE user_id IN (SELECT user_id FROM message_to_user WHERE message_id=:mid)');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            for($i=0, $len=sizeof($rows); $i<$len; $i++){
                $message['to-users'][]=['username'=>$rows[$i]['username']];
            }

            $statement=$pdo->prepare('SELECT projectname, url FROM projects WHERE project_id IN (SELECT project_id FROM message_to_project WHERE message_id=:mid)');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            for($i=0, $len=sizeof($rows); $i<$len; $i++){
                $message['to-projects'][]=['projectname'=>$rows[$i]['projectname'], 'url'=>$rows[$i]['url']];
            }

            $pdo->commit();
            unset ($pdo);
            return $message;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
            unset($pdo);
            return false;
        }
    }

    public static function selectMessage($from_username, $create_time){
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
            $statement=$pdo->prepare('SELECT ua.username AS from_username, pr.projectname AS from_projectname, pr.url AS from_url, msg.message_id, msg.subject, msg.message, msg.send_time, msg.read_time, msg.create_time FROM messages AS msg
                LEFT JOIN user_accounts AS ua ON msg.from_user_id=ua.user_id
                LEFT JOIN projects AS pr ON pr.project_id=msg.from_project_id
                WHERE msg.message_id IN
                (SELECT message_id FROM messages
                    WHERE create_time=:ct
                    AND from_user_id IN (
                        SELECT user_id FROM user_accounts WHERE username=:un
                    )
                )
            ');
            $statement->bindValue(':un',strval($from_username), PDO::PARAM_STR);
            $statement->bindValue(':ct',strval($create_time), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $message = isset($rows[0]) ?
                [
                    'from-user'=>[
                        'username'=>$rows[0]['from_username']
                    ],
                    'from-project'=>(
                    (isset($rows[0]['from_projectname'], $rows[0]['from_url']) && $rows[0]['from_projectname'] && $rows[0]['from_url'])
                    ?[
                        'projectname'=>$rows[0]['from_projectname'],
                        'url'=>$rows[0]['from_url']
                    ]
                    :null
                    ),
                    'subject'=>$rows[0]['subject'],
                    'message'=>$rows[0]['message'],
                    'send-time'=>$rows[0]['send_time'],
                    'read-time'=>$rows[0]['read_time'],
                    'create-time'=>$rows[0]['create_time'],
                    'to-users'=>[],
                    'to-projects'=>[]
                ]
            : false;

            if($message==false){
                $pdo->commit();
                unset($pdo);
                return false;
            }

            $message_id=$rows[0]['message_id'];
                /* 
                LEFT JOIN message_to_user AS mtu ON mtu.message_id=msg.message_id
                LEFT JOIN user_accounts AS uato ON uato.user_id=mtu.user_id
                LEFT JOIN message_to_project AS mtp ON mtp.message_id=msg.message_id
                LEFT JOIN projects AS prto ON prto.project_id=mtp.project_id
                */
            $statement=$pdo->prepare('SELECT username FROM user_accounts WHERE user_id IN (SELECT user_id FROM message_to_user WHERE message_id=:mid)');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            for($i=0, $len=sizeof($rows); $i<$len; $i++){
                $message['to-users'][]=['username'=>$rows[$i]['username']];
            }

            $statement=$pdo->prepare('SELECT projectname, url FROM projects WHERE project_id IN (SELECT project_id FROM message_to_project WHERE message_id=:mid)');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            for($i=0, $len=sizeof($rows); $i<$len; $i++){
                $message['to-projects'][]=['projectname'=>$rows[$i]['projectname'], 'url'=>$rows[$i]['url']];
            }

            $pdo->commit();
            unset ($pdo);
            return $message;

        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
            unset($pdo);
            return false;
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

    public static function insertMessage($values){
    /***
    insertMessage($values)
        $values = [from_project, from_user, to_users=[username, username], to_projects=[url, url], subject, message, sent=true/false, ]

    ***/
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

            //insert the message to table 'messages'
            //there might be issue with inserting message only from user
            $from_project = (isset($values['from_project']) && $values['from_project'] !== null) ? true : false; 
            if($from_project){
                $statement=$pdo->prepare('INSERT INTO messages (from_user_id, from_project_id, subject, message, create_time, send_time)
                    (SELECT up.user_id, up.project_id, :sub, :msg, UNIX_TIMESTAMP(), '.($values['sent']===true?'UNIX_TIMESTAMP()':'NULL').' FROM project_user AS up
                        WHERE up.user_id IN
                        (SELECT user_id FROM user_accounts WHERE username=:from_un)
                        AND up.project_id IN
                        (SELECT project_id FROM projects WHERE url=:from_pr)
                        AND up.relationship IN (\'admin\',\'member\')
                    )
                ');
                $statement->bindValue(':from_pr',strval($values['from_project']), PDO::PARAM_STR);
            }
            else{
                $statement=$pdo->prepare('INSERT INTO messages (from_user_id, subject, message, create_time, send_time)
                    SELECT ua.user_id, :sub, :msg, UNIX_TIMESTAMP(), '.($values['sent']===true?'UNIX_TIMESTAMP()':'NULL').' FROM user_accounts AS ua WHERE ua.username=:from_un');
            }
            $statement->bindValue(':sub',strval($values['subject']), PDO::PARAM_STR);
            $statement->bindValue(':msg',strval($values['message']), PDO::PARAM_STR);
            $statement->bindValue(':from_un',strval($values['from_user']), PDO::PARAM_STR);
            $statement->execute();
            //get id of inserted message
            $message_id = $pdo->lastInsertId();
            unset($statement);
            
            //insert receivers - users to table 'message_to_user'
            $to_users=$values['to_users'];
            for($i=0, $len=sizeof($to_users); $i<$len; $i++){
                //insert into message_to_user
                $statement=$pdo->prepare('INSERT INTO message_to_user (message_id, user_id)
                    SELECT :msg_id, ua.user_id FROM user_accounts AS ua WHERE ua.username=:un');
                $statement->bindValue(':msg_id',strval($message_id), PDO::PARAM_STR);
                $statement->bindValue(':un',strval($to_users[$i]), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);
            }

            //insert receivers: projects to table 'message_to_project'
            $to_projects=$values['to_projects'];
            for($i=0, $len=sizeof($to_projects); $i<$len; $i++){
                //insert into message_to_project
                $statement=$pdo->prepare('INSERT INTO message_to_project (message_id, project_id)
                    SELECT :msg_id, pr.project_id FROM projects AS pr WHERE pr.url=:url');
                $statement->bindValue(':msg_id',strval($message_id), PDO::PARAM_STR);
                $statement->bindValue(':url',strval($to_projects[$i]), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);
            }

            $statement=$pdo->prepare('SELECT msg.create_time AS ct, ua.username AS un FROM messages AS msg INNER JOIN user_accounts AS ua ON msg.from_user_id=ua.user_id WHERE msg.message_id=:msg_id ');
            $statement->bindValue(':msg_id', strval($message_id), PDO::PARAM_STR);
            $statement->execute();

            $ids = $statement->fetchAll(PDO::FETCH_ASSOC);

            $identification = isset($ids[0]) ? ['username'=>$ids[0]['un'], 'timestamp' => $ids[0]['ct']] : false;
            unset($statement);
  // 
            $pdo->commit();

            unset ($pdo);
            return $identification;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            // Report errors
            echo $outcome;
            return false;
        }
        unset($pdo);
    }

    public static function deleteMessageById($message_id){
        $pdo=self::newPDO();
        $pdo->beginTransaction();
        // 
        try
        {
            $statement=$pdo->prepare('DELETE FROM message_to_user WHERE message_id=:mid');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            $statement=$pdo->prepare('DELETE FROM message_to_project WHERE message_id=:mid');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();
            
            // Prepare the statements
            $statement=$pdo->prepare('DELETE FROM messages WHERE message_id=:mid
            ');
            $statement->bindValue(':mid',strval($message_id), PDO::PARAM_STR);
            $statement->execute();

            $deleted_num=$statement->rowCount();
            
            $pdo->commit();
            unset ($pdo);
            return $deleted_num > 0 ? true : false;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
            unset($pdo);
            return false;
        }
    }
}
