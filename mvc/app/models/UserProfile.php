<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/UserInfo.php';
require_once dirname(__FILE__).'/database/Tags.php';
require_once dirname(__FILE__).'/database/ProjectUser.php';
require_once dirname(__FILE__).'/User.php';

class UserProfile extends User
{
    /*
    what data do we want to enter to database?
        about, location, birthday, website, links to profiles on famous social networks (bewelcome, couchsurfing, facebook, twitter, linked_in, ...)
    what is this class used for?
        get profile data of user from database (for everybody)
        update profile data in database (for user herself);


    what functions do we need to develop?
        getProfile();   //get profile data from database;
        validate();     //validate profile data entered by user;
        updateProfile();    //insert profile data to database;
    */
    private $info = '';
    private $web = array();
    private $birthday = '';
    private $location = '';

    public function getProfile($username=''){
        $username = /*($username==='') ? $this->username :*/ $username;
        $profile = Database\UserInfo::selectProfile($username);
        if(is_array($profile)){
            $profile['age'] = floor((time() - strtotime($profile['birthday'])) / 31556926);
            $profile['member-since'] = date('F Y',$profile['account_created']);
            $profile['last-login'] = self::generateLastLogin($profile['last_login']);

            unset($profile['birthday'], $profile['account_created'], $profile['last_login']);
        }
        
        //FAKE
//        $profile['age'] = 16;
//        $profile['v_age'] = true;
//        $profile['gender'] = 'hermafrodite';
//        $profile['v_gender'] = true;
//        $profile['website'] = 'http://example.com';
//        $profile['v_website'] = true;
//        $profile['bewelcome'] = 'mrkvon';
//        $profile['v_bewelcome'] = true;
//        $profile['couchsurfing'] = 'mrkvon';
//        $profile['v_couchsurfing'] = true;
//        $profile['facebook'] = '';
//        $profile['v_facebook'] = false;
//        $profile['twitter'] = '';
//        $profile['v_twitter'] = false;
//        $profile['views'] = 1358;
        

        return $profile;
    }

    private static function generateLastLogin($seconds){
        if($seconds!==null){
            $seconds*=1;
            //exit(gettype($seconds));
            if($seconds<60) return $seconds.($seconds   ==1?' second ago'   :' seconds ago');
            $minutes=floor($seconds/60);
            if($minutes<60) return $minutes.($minutes   ==1?' minute ago'   :' minutes ago');
            $hours=floor($minutes/60);
            if($hours<24)   return $hours.  ($hours     ==1?' hour ago'     :' hours ago');
            $days=floor($hours/24);
            if($days<30)    return $days.   ($days      ==1?' day ago'      :' days ago');
            $months=floor($days/30);
            $years=floor($days/365);
            if($days<365)   return $months. ($months    ==1?' month ago'    :' months ago');
            return $years.  ($years     ==1?' year ago'     :' years ago');
        }
        else return '?';
    }
    
    public function setProfile($data){
        //print_r($data);
        $updated=Database\UserInfo::updateProfile($data);
        if(!$updated) throw new Exception('updating error');
    }
    
    public static function getTags($username){
        $tags = Database\Tags::selectTagsByUsername($username);
        return $tags;
    }

    public static function addTag($tagname, $username){
        /***bool addTag(string $tagname, string $username)
            add tag (tagname) to user (username)
            return true on success and false on fail
        **/    
    }
    
    public static function removeTag($tagname, $username){
        /***bool removeTag(string $tagname, string $username)
            remove tag $tagname from user $username
            return true on success and false on fail
        ***/ 
    }

    public function validate($data, &$errors){
        $ret = true;

        return $ret;
    }

    public static function getProjects($username){
        $projects = Database\ProjectUser::selectProjectsByUsername($username);
        return $projects;
    }
}
