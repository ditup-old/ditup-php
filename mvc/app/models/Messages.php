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

    public static function saveDraftMessage($values){
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

    public static function readMessage($username, $timestamp, $request_username){
        if(Database\Messages::rightToRead($username, $timestamp, $request_username)){
            return Database\Messages::selectMessage($username, $timestamp);
        }
        else return false;
    }

    public static function getReceivedMessages($username){
        $ids=Database\Messages::selectMessageIdsOfUser($username);
        $messages=[];
        for($i=0, $len=sizeof($ids); $i<$len; $i++){
            $messages[]=Database\Messages::SelectMessageById($ids[$i]);
        }
        return $messages;
    }
}
