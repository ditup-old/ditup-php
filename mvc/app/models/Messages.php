<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/Messages.php';

class Messages
{
    public static function validate($values, &$errors){
        return true;
    }

    public static function sendMessage($values){
        //either send draft -> update and add send-time in database
        //or send new message -> insert into database

        //if this was a draft
        if(isset($values['create-time'])&& $values['create-time']!==''){
            //send draft - update in database
            $message_id = Database\Messages::selectMessageId($values['from-user'], $values['create-time']);

            $success=Database\Messages::updateMessageById($message_id, [
                'from_project' => $values['from-project'],
                'from_user' => $values['from-user'],
                'to_users' => $values['to-users'],
                'to_projects' => $values['to-projects'],
                'subject' => $values['subject'],
                'message' => $values['message'],
                'sent' => true
            ]);
            return $success;
            
        }
        else{
            $success=Database\Messages::insertMessage([
                'from_project' => $values['from-project'],
                'from_user' => $values['from-user'],
                'to_users' => $values['to-users'],
                'to_projects' => $values['to-projects'],
                'subject' => $values['subject'],
                'message' => $values['message'],
                'sent' => true
            ]);
            return $success;
        }
    }

    public static function saveDraftMessage($values){
        //if this was a draft
        if(isset($values['create-time'])&& $values['create-time']!==''){
            //send draft - update in database
            $message_id = Database\Messages::selectMessageId($values['from-user'], $values['create-time']);

            $success=Database\Messages::updateMessageById($message_id, [
                'from_project' => $values['from-project'],
                'from_user' => $values['from-user'],
                'to_users' => $values['to-users'],
                'to_projects' => $values['to-projects'],
                'subject' => $values['subject'],
                'message' => $values['message'],
                'sent' => false
            ]);
            return $success ? ['username'=> $values['from-user'], 'timestamp' => $values['create-time']] : false;
            
        }
        else{
            $success = Database\Messages::insertMessage([
                'from_project' => $values['from-project'],
                'from_user' => $values['from-user'],
                'to_users' => $values['to-users'],
                'to_projects' => $values['to-projects'],
                'subject' => $values['subject'],
                'message' => $values['message'],
                'sent' => false
            ]);
            return $success;
        }
    }

    public static function readMessage($username, $timestamp, $request_username){
        if(Database\Messages::rightToRead($username, $timestamp, $request_username)){
            return Database\Messages::selectMessage($username, $timestamp, $request_username);
        }
        else return false;
    }

    public static function getReceivedMessages($username){
        $ids=Database\Messages::selectMessageIdsOfUser($username, 'RECEIVED');
        $messages=[];
        for($i=0, $len=sizeof($ids); $i<$len; $i++){
            $messages[]=Database\Messages::SelectMessageById($ids[$i], $username);
        }
        return $messages;
    }

    public static function getSentMessages($username){
        $ids=Database\Messages::selectMessageIdsOfUser($username, 'SENT');
        $messages=[];
        for($i=0, $len=sizeof($ids); $i<$len; $i++){
            $messages[]=Database\Messages::SelectMessageById($ids[$i], $username);
        }
        return $messages;
    }

    public static function getDraftMessages($username){
        $ids=Database\Messages::selectMessageIdsOfUser($username, 'DRAFTS');
        $messages=[];
        for($i=0, $len=sizeof($ids); $i<$len; $i++){
            $messages[]=Database\Messages::SelectMessageById($ids[$i], $username);
        }
        return $messages;
    }

    public static function deleteMessage($username, $timestamp){
        $message_id=Database\Messages::selectMessageId($username, $timestamp);
        if($message_id){
            $success=Database\Messages::deleteMessageById($message_id);
            return $success ? true : false;
        }
        else return false;
    }
}
