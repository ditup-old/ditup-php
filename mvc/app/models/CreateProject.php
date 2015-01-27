<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/Project.php';
require_once dirname(__FILE__).'/database/Projects.php';

class CreateProject extends Project
{
    const PROJECTNAME_MAX_LENGTH = 64;
    const PROJECTNAME_MIN_LENGTH = 1;
    const PROJECTNAME_LENGTH_ERROR = 'project name must be between 1 and 64 characters long';
    const PROJECTNAME_UNIQUE_ERROR = 'projectname must be unique (this one already exists)';
    const URL_MAX_LENGTH = 64;
    const URL_MIN_LENGTH = 1;
    const URL_LENGTH_ERROR = 'project url must be between 1 and 64 characters long';
    const URL_UNIQUE_ERROR = 'url must be unique (this one already exists)';
    const URL_REGEX = '/^([a-z0-9\-]*)$/';
    const URL_REGEX_ERROR = 'url can contain only a-z, 0-9, "-"';
    const SUBTITLE_LENGTH = 256;
    const SUBTITLE_LENGTH_ERROR = 'subtitle can be max 256 characters long';
    const DESCRIPTION_LENGTH = 1024;
    const DESCRIPTION_LENGTH_ERROR = 'description can be max 1024 characters long';
    const DIT_TYPES = ['idea', 'project', 'interest', 'topic', 'issue'];
    const DIT_TYPE_ERROR = 'wrong dit type. select from options provided.';

    /**
     * General function for entering new project to database. Returns true on success or throws an error. 
     * @param $data Array (projectname, url, subtitle, description, create)
     * @return boolean
     */
    public function create($data, &$errors){
        if(self::validate($data, $errors)){
            
            $database_projects = new Database\Projects;
            $database_projects->insertProject($data);
            unset($database_projects);
            return true;
        }
        else{
            return false;
        }
    }

    private function validate($values, &$errors){
        /***
         * requirements:
         * submit value exists
         * projectname has limited length
         * project url is unique
         * project url has limited length
         */
        $errors=[];
        $ret=true;
        if(!isset($values['create'])){
            $ret = false;
            $errors['create'] = 'submit error';
        }
        $pn_errors = Array();
        if(!self::validateName($values['projectname'], $pn_errors)){
            $ret = false;
            $errors['projectname']=isset($errors['projectname'])?$errors['projectname']:[];
            foreach($pn_errors as $err){
                $errors['projectname'][] = $err;
            }
        }
        $pn_errors = [];
        if(self::projectnameExists($values['projectname'])){
            $ret = false;
            if(isset($errors['projectname'])){
                $errors['projectname'][] = self::PROJECTNAME_UNIQUE_ERROR;
            }
            else{
                $errors['projectname']=[self::PROJECTNAME_UNIQUE_ERROR];
            }
        }
        $url_errs=[];
        if(!self::validateUrl($values['url'], $url_errs)){
            $ret = false;
            $errors['url'] = isset($errors['url'])?$errors['url']:[];
            foreach($url_errs as $urer){
                $errors['url'][]=$urer;
            }
        }
        if(self::urlExists($values['url'])){
            $ret = false;
            $errors['url'] = isset($errors['url'])?$errors['url']:[];
            $errors['url'][] = self::URL_UNIQUE_ERROR;
        }
        if(!self::validateSubtitle($values['subtitle'])){
            $ret = false;
            $errors['subtitle'] = 'length??';
        }
        if(!self::validateDescription($values['description'])){
            $ret = false;
            $errors['description'] = '???';
        }
        if(!in_array($values['type'], self::DIT_TYPES)){
            $ret = false;
            $errors['type'] = self::DIT_TYPE_ERROR;
        }
        return $ret;
    }

    private static function validateName($projectname, &$pn_errors){
        $ret=true;
        //$username_match='/^([a-z0-9_\-\.]{2,16})$/';
        //return preg_match($username_match, $username)?true:false;
        if (strlen($projectname) > self::PROJECTNAME_MAX_LENGTH || strlen($projectname) < self::PROJECTNAME_MIN_LENGTH){
            array_push($pn_errors, self::PROJECTNAME_LENGTH_ERROR);
            $ret = false;
        }
        return $ret;
    }

    private static function validateUrl($url, Array &$url_errors=[]) {
        $ret=true;
        if(!preg_match(self::URL_REGEX, $url)){
            $ret=false;
            $url_errors[]=self::URL_REGEX_ERROR;
        }
        if (strlen($url) > self::URL_MAX_LENGTH || strlen($url) < self::URL_MIN_LENGTH){
            $url_errors[] = self::URL_LENGTH_ERROR;
            $ret = false;
        }
        return $ret;
    }

    private static function validateSubtitle($subtitle) {
        return strlen($subtitle) <= self::SUBTITLE_LENGTH;
    }


    private static function validateDescription($description) {
        return strlen($description) <= self::DESCRIPTION_LENGTH;
    }

    public static function urlExists($url){
        $database_projects = new Database\Projects;
        $project = $database_projects->selectProjectByUrl($url);
        unset($database_projects);
        return is_array($project);
    }

    public static function projectnameExists($projectname, &$pn_errors = []){
        $database_projects = new Database\Projects;
        $project = $database_projects->selectProjectByName($projectname);
        unset($database_projects);
        return is_array($project);
    }
}
