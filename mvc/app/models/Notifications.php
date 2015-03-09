<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/Projects.php';
require_once dirname(__FILE__).'/database/ProjectUser.php';
require_once dirname(__FILE__).'/database/Notifications.php';

class Notifications
{
    public static function getNotifications($username){
        //$notices=array_merge(self::getJoinNotifications($username));
        $notices=Database\Notifications::selectNotifications($username);
        if($notices===false) exit('Database\Notifications::selectNotifications database error');
        $processed=[];

        foreach($notices as $note){
            if($note['type']==='await-join'){
                $pro=self::processJoinRequestNotification($note);
            }

            $pro['id']=$note['id'];
//            exit(print_r($note, true));
            $pro['view-time']=$note['view_time'];
            $processed[]=$pro;
        }

        return $processed;
    }

    public static function countNotifications($username){
        return Database\Notifications::countNewNotifications($username);
    }

/*
    public static function createJoinNotification($await_username, $dit_url){
        //currently not used
        $awaiting=Database\Notifications::insertNotification($type, ['about-user' => $await_username, 'about-dit' =>$dit_url]);
    }
//*/

    public static function createJoinRequestNotifications($about_username, $about_dit_url){
        //this function should be used when user requests joining some group
        //it should create join-request notifications for everybody who has right to accept new members (i.e. admins)
        $success=Database\Notifications::insertNotifications($type, ['about-user' => $about_username, $about_dit_url]);
        return $success;
    }

    public static function deleteNotification($notification_id, $username){
        //****this function should be used to delete notification by its owner $username
        $success=Database\Notifications::deleteNotification($notification_id, $username);
        return $success;
    }

    public static function processNotification($notification_id, $username){
        self::viewNotification($notification_id, $username);
        $notif=self::readNotification($notification_id, $username);
        if($notif===false || $notif===null) exit('notification does not exist or database problem');
        return self::makeUrl($notif);
    }

    private static function viewNotification($notification_id, $username){
        //this function should update notification view time to current unix timestamp if it was not viewed before
        $success=Database\Notifications::updateNotificationViewTime($notification_id, $username);
        return $success;
    }

    private static function readNotification($notification_id, $username){
        //this function should get notification data from database
        $success=Database\Notifications::selectNotification($notification_id, $username);
        return $success;
    }

    public static function visitNotifications($username){
        //if user $username visits /notifications, this will be updated...
        $success=Database\UserAccounts::updateVisitNotifications($username);
        return $success;
    }

    public static function deleteJoinRequestNotifications($about_username, $about_dit_url){
    
    }
    
    private static function processJoinRequestNotification($data){
        $ret=[
            'text' => 'User '.$data['username'].' wants to join '.$data['dittype'].':'.$data['ditname'].'.',
            'url' => '/'.$data['dittype'].'/'.$data['url'].'/join-request/'.$data['username']
        ];
        return $ret;
    }

    private static function makeUrl($data){
        if($data['type']==='await-join'){
            return '/'.$data['dittype'].'/'.$data['url'].'/join-request/'.$data['username'];
        };
    }
}
