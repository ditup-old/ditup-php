<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/Projects.php';
require_once dirname(__FILE__).'/database/ProjectUser.php';

class DitUser
{
    public static function joinQuestion($url){
        return 'implement retrieving join question for dit '.$url;
    }

    public static function joinAnswer($url, $username){
	$answer=Database\ProjectUser::selectJoinMessage($url, $username);
	if($answer===false) return 'database error';
	$default_answer='default: user '.$username.' didn\'t write anything or doesn\'t want to join';
        return $answer!==null ? $answer : $default_answer;
    }

    public static function isMember($url, $username){
        $relations=Database\Projects::getRelations($url, $username);
        return in_array('member', $relations) || in_array('admin', $relations);
    }

    public static function isAdmin($url, $username){
        $relations=Database\Projects::getRelations($url, $username);
        return in_array('admin', $relations);
    }

    public static function isAwaiting($username, $url){
        $relations=Database\Projects::getRelations($url, $username);
        return in_array('await-member', $relations);
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

    public static function getAwaitMembers($url){
        $await_members=Database\Projects::getPeople($url, 'AWAIT_MEMBERS');
        $ret=[];
        foreach($await_members as $person){
            $ret[]=$person['username'];
        }
//        print_r($ret);
        return $ret;
    }

    public static function getUserMembership($username, $dit_url){
        $relation=Database\ProjectUser::selectDitRelationshipByUsername($username, $dit_url);
        if($relation===false){
            exit('unexpected problem in database');
        }
        if(is_array($relation)){
            return $relation;
        }
    }

    public static function sendJoinRequest($username, $dit_url, $form_data, &$errors){
        $err='';
        if(self::validateJoinRequest($form_data, $errors)){
            $send_result=Database\ProjectUser::insertAwaitMember($username, $dit_url, $form_data['request-message'], $err);
            if($send_result===false) $errors['submit']=$err;

            return $send_result===true ? true : false;
        }
        else return false;
    }
    
    public static function acceptUserToDit($username, $url){
        $accept_result=Database\ProjectUser::updateAwaitMemberToMember($username, $url);
        return $accept_result === true ? true : false;
    }

    public static function declineUserToDit($username, $url){
        $decline_result=Database\ProjectUser::deleteAwaitMember($username, $url);
        return $decline_result === true ? true : false;
    }

    private static function validateJoinRequest($form_data, &$errors){
        //join_message must have limited length (check database);
        $ret=true;
        if(strlen($form_data['request-message'])>self::JOIN_MESSAGE_LENGTH){
            $errors['request-message'] = self::JOIN_MESSAGE_LENGTH_ERROR;
            $ret=false;
        }
        return $ret;
    }
}
