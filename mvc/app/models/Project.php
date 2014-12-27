<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/Projects.php';

class Project
{
    /*
        function returns true if project exists in database
    */
    public static function exists($url){
        $project=Database\Projects::selectProjectByUrl($url);
        return is_array($project);
    }

    public static function getProjectByUrl($url){
        $project=Database\Projects::selectProjectByUrl($url);
        if(is_array($project)){
            return $project;
        }
        else{
            throw new Exception('profile does not exist');
        }
    }

    public static function isMember($url, $username){
        $relations=Database\Projects::getRelations($url, $username);
        return in_array('member', $relations) || in_array('admin', $relations);
    }

    public static function isAdmin($url, $username){
        $relations=Database\Projects::getRelations($url, $username);
        return in_array('admin', $relations);
    }

    public static function getMembers($url){
        $people=Database\Projects::getPeople($url);
        $ret=[];
        foreach($people as $person){
            if($person['relationship']=='member' || $person['relationship']=='admin'){
                $ret[]=$person['username'];
            }
        }
        //print_r($ret);
        return $ret;
    }

    public static function getAdmins($url){
        $people=Database\Projects::getPeople($url);
        $ret=[];
        foreach($people as $person){
            if($person['relationship']=='admin'){
                $ret[]=$person['username'];
            }
        }
        return $ret;
    }

    public static function getLocation($projectname){
        return 'implement!!';
    }
}
