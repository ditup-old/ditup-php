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
        $notices=array_merge(self::getJoinNotifications($username));
        return $notices;
    }

    public static function countNotifications($username){
        return Database\Notifications::countNewNotifications($username);
    }

    public static function createJoinNotification($for_username, $await_username, $dit_url){
        $awaiting=Database\Notifications::insertNotification($username, $type, ['about-user' => $await_username, 'about-dit' =>$dit_url]);
    }

    private static function getJoinNotifications($username){
        $awaiting=Database\ProjectUser::selectJoinNotifications($username);
        $ret=[];
        foreach($awaiting as $wait){
            $ret[]=[
                'text' => 'User '.$wait['username'].' wants to join '.$wait['type'].':'.$wait['ditname'].'.',
                'url' => '/'.$wait['type'].'/'.$wait['url'].'/join-request/'.$wait['username']
            ];
        }
        return $ret;
    }
}
