<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/Projects.php';
require_once dirname(__FILE__).'/database/ProjectUser.php';

class Notifications
{
    public static function getNotifications($username){
        $notices=array_merge(self::getJoinNotifications($username));
        return $notices;
    }

    public static function countNotifications($username){
        return 5;
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
