<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/UserInfo.php';
require_once dirname(__FILE__).'/database/Tags.php';
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
    
    public function setProfile($data){
        print_r($data);
        $updated=Database\UserInfo::updateProfile($data);
        if(!$updated) throw new Exception('updating error');
    }
    
    public static function getTags($username){
        $tags = Database\Tags::selectTagsByUsername($username);
        return $tags;
    }

    public function validate($data, &$errors){
        $ret = true;

        return $ret;
    }
}
